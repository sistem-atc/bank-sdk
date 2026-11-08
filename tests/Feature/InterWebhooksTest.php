<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use SistemAtc\Banks\Banks\Inter\Enums\Escopo;
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhook;
use SistemAtc\Banks\Banks\Inter\Inter;
use SistemAtc\Banks\Banks\Inter\Support\OAuth;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;
use SistemAtc\Banks\Webhooks\BankWebhookReceived;
use SistemAtc\Banks\Webhooks\WebhookUrl;

/** Integração Inter autenticada, em produção (host cdpj.partners). */
function interWebhookIntegration(array $settings = []): FakeBankIntegration
{
    Http::preventStrayRequests();

    $i = new FakeBankIntegration(settings: $settings, sandbox: false);
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 3600;

    return $i;
}

/** @return array<int, BankWebhookReceived> */
function capturarAvisos(): ArrayObject
{
    $recebidos = new ArrayObject();
    Event::listen(BankWebhookReceived::class, fn (BankWebhookReceived $e) => $recebidos->append($e));

    return $recebidos;
}

beforeEach(fn () => config(['app.url' => 'https://erp.exemplo.com.br']));

// ── Recebimento ────────────────────────────────────────────────────────────

it('o SDK registra a rota de webhook do Inter sozinho', function () {
    expect(Route::has('banks.webhooks.inter'))->toBeTrue();
});

it('recebe o aviso autenticado pelo proxy e entrega ao host pelo evento', function () {
    $avisos = capturarAvisos();

    $this->postJson('/api/webhooks/banks/inter/42/cobranca', [['codigoSolicitacao' => 'abc', 'situacao' => 'RECEBIDO']], [
        'X-SSL-Client-Verify' => 'SUCCESS',
        'x-conta-corrente' => '571314970',
    ])->assertOk()->assertJson(['ok' => true]);

    expect($avisos)->toHaveCount(1);
    $aviso = $avisos[0];
    expect($aviso->bank)->toBe('inter')
        ->and($aviso->integrationId)->toBe('42')
        ->and($aviso->tipo)->toBe('cobranca')
        ->and($aviso->payload)->toBe([['codigoSolicitacao' => 'abc', 'situacao' => 'RECEBIDO']])
        ->and($aviso->headers)->toBe(['x-conta-corrente' => '571314970'])
        ->and($aviso->rawBody)->toContain('codigoSolicitacao');
});

it('aceita o /pix que o Inter acrescenta à URL do webhook de Pix recebido', function () {
    $avisos = capturarAvisos();

    $this->postJson('/api/webhooks/banks/inter/42/pix/pix', ['pix' => [['endToEndId' => 'E1', 'valor' => '10.00']]], [
        'X-SSL-Client-Verify' => 'SUCCESS',
    ])->assertOk();

    expect($avisos[0]->tipo)->toBe('pix');
});

it('recusa aviso sem o certificado do Inter conferido pelo proxy', function () {
    $avisos = capturarAvisos();

    $this->postJson('/api/webhooks/banks/inter/42/cobranca', [['x' => 1]])->assertStatus(401);
    $this->postJson('/api/webhooks/banks/inter/42/cobranca', [['x' => 1]], ['X-SSL-Client-Verify' => 'FAILED:unable to verify'])->assertStatus(401);

    expect($avisos)->toHaveCount(0);
});

it('sem cabeçalho configurado a conferência fica desligada', function () {
    config(['banks.webhooks.inter.client_verify_header' => '']);
    $avisos = capturarAvisos();

    $this->postJson('/api/webhooks/banks/inter/42/boleto-pagamento', [['codigoTransacao' => 'T1']])->assertOk();

    expect($avisos)->toHaveCount(1);
});

it('tipo desconhecido é 404 e corpo que não é JSON é 422', function () {
    capturarAvisos();
    $headers = ['X-SSL-Client-Verify' => 'SUCCESS'];

    $this->postJson('/api/webhooks/banks/inter/42/inventado', ['a' => 1], $headers)->assertNotFound();
    $this->call('POST', '/api/webhooks/banks/inter/42/cobranca', [], [], [], ['HTTP_X_SSL_CLIENT_VERIFY' => 'SUCCESS', 'CONTENT_TYPE' => 'text/plain'], 'não é json')
        ->assertStatus(422);
});

it('sem listener no host responde 503 em vez de engolir o aviso', function () {
    $this->postJson('/api/webhooks/banks/inter/42/cobranca', [['a' => 1]], ['X-SSL-Client-Verify' => 'SUCCESS'])
        ->assertStatus(503);
});

it('falha ao gravar no host vira 500, e o Inter reenvia', function () {
    $this->withoutExceptionHandling();
    Event::listen(BankWebhookReceived::class, fn () => throw new RuntimeException('banco fora'));

    expect(fn () => $this->postJson('/api/webhooks/banks/inter/42/cobranca', [['a' => 1]], ['X-SSL-Client-Verify' => 'SUCCESS']))
        ->toThrow(RuntimeException::class, 'banco fora');
});

// ── URL e cadastro ─────────────────────────────────────────────────────────

it('monta a URL pública com app.url, ou com o base_url quando informado', function () {
    expect(WebhookUrl::for('inter', 42, 'pix'))->toBe('https://erp.exemplo.com.br/api/webhooks/banks/inter/42/pix');

    config(['banks.webhooks.base_url' => 'https://webhooks.exemplo.com.br/']);

    expect(Inter::make()->webhooks(new FakeBankIntegration())->url(TipoWebhook::Cobranca))
        ->toBe('https://webhooks.exemplo.com.br/api/webhooks/banks/inter/1/cobranca');
});

it('cadastra cada tipo na API certa do Inter, com a URL do SDK', function () {
    $i = interWebhookIntegration();
    Http::fake(['*' => Http::response(null, 204)]);

    $webhooks = Inter::make()->webhooks($i);
    $webhooks->cadastrar(TipoWebhook::Cobranca);
    $webhooks->cadastrar(TipoWebhook::PixPagamento);
    $webhooks->cadastrar(TipoWebhook::BoletoPagamento);
    $webhooks->cadastrar(TipoWebhook::PixRecebido, chavePix: 'financeiro@empresa.com.br');

    $base = 'https://cdpj.partners.bancointer.com.br';
    $url = fn (string $tipo) => "https://erp.exemplo.com.br/api/webhooks/banks/inter/1/{$tipo}";

    Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->url() === "{$base}/cobranca/v3/cobrancas/webhook" && $r['webhookUrl'] === $url('cobranca'));
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->url() === "{$base}/banking/v2/webhooks/pix-pagamento" && $r['webhookUrl'] === $url('pix-pagamento'));
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->url() === "{$base}/banking/v2/webhooks/boleto-pagamento" && $r['webhookUrl'] === $url('boleto-pagamento'));
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->url() === "{$base}/pix/v2/webhook/financeiro%40empresa.com.br" && $r['webhookUrl'] === $url('pix'));
});

it('webhook de Pix recebido exige a chave Pix', function () {
    expect(fn () => Inter::make()->webhooks(interWebhookIntegration())->cadastrar(TipoWebhook::PixRecebido))
        ->toThrow(InvalidArgumentException::class, 'chave Pix');
});

it('consultar devolve a URL cadastrada, ou null quando não há webhook', function () {
    $i = interWebhookIntegration();
    Http::fake([
        '*/cobranca/v3/cobrancas/webhook' => Http::response(['webhookUrl' => 'https://x/cob', 'criacao' => '2026-11-08T10:00:00Z']),
        '*/banking/v2/webhooks/pix-pagamento' => Http::response(['title' => 'Não encontrado'], 404),
    ]);

    $webhooks = Inter::make()->webhooks($i);

    expect($webhooks->consultar(TipoWebhook::Cobranca))->toBe('https://x/cob')
        ->and($webhooks->consultar(TipoWebhook::PixPagamento))->toBeNull();
});

it('sincronizar cadastra os desejados, remove os que saíram e isola a falha de um tipo', function () {
    $i = interWebhookIntegration();
    Http::fake([
        '*/oauth/v2/token' => Http::response(['access_token' => 'NOVO', 'expires_in' => 3600]),
        '*/cobranca/v3/cobrancas/webhook' => Http::response(null, 204),
        '*/banking/v2/webhooks/pix-pagamento' => Http::response(['title' => 'Forbidden', 'detail' => 'Escopo não autorizado'], 403),
        '*/banking/v2/webhooks/boleto-pagamento' => Http::response(null, 204),
    ]);

    $resultado = Inter::make()->webhooks($i)->sincronizar(
        desejados: [TipoWebhook::Cobranca, 'pix-pagamento'],
        anteriores: ['boleto-pagamento', 'cobranca'],
    );

    expect($resultado['cobranca'])->toMatchArray(['acao' => 'cadastrar', 'ok' => true])
        ->and($resultado['pix-pagamento'])->toMatchArray(['acao' => 'cadastrar', 'ok' => false])
        ->and($resultado['pix-pagamento']['detalhe'])->toContain('Escopo não autorizado')
        ->and($resultado['boleto-pagamento'])->toMatchArray(['acao' => 'excluir', 'ok' => true]);

    Http::assertSent(fn (Request $r) => $r->method() === 'DELETE' && str_ends_with($r->url(), '/banking/v2/webhooks/boleto-pagamento'));
});

// ── Escopos ────────────────────────────────────────────────────────────────

it('o enum de escopos cobre o padrão do config e entra no token como enum ou texto', function () {
    expect(config('banks.inter.scopes'))->toBe(Escopo::values())
        ->and(Escopo::options()['extrato.read'])->toBe('Consultar extrato e saldo (extrato.read)')
        ->and(Escopo::BoletoCobrancaEscrita->api())->toBe('Cobrança')
        ->and(TipoWebhook::PixRecebido->escopo())->toBe(Escopo::WebhookPixEscrita);

    $comEnum = new FakeBankIntegration(settings: ['scopes' => [Escopo::ExtratoLeitura, 'pix.read', Escopo::ExtratoLeitura]]);
    $comTexto = new FakeBankIntegration(settings: ['scopes' => 'extrato.read pix.read']);

    expect(OAuth::scopes($comEnum))->toBe(['extrato.read', 'pix.read'])
        ->and(OAuth::scopes($comTexto))->toBe(['extrato.read', 'pix.read']);
});
