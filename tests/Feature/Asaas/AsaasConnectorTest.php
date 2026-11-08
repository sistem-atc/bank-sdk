<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Contracts\WebhookIntegrationResolver;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Gateway;
use SistemAtc\Banks\Gateways\Asaas\Asaas;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\PaymentWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;
use SistemAtc\Banks\Gateways\Asaas\Webhooks\AsaasWebhookPayload;
use SistemAtc\Banks\Tests\Fakes\FakeAsaasIntegration;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;
use SistemAtc\Banks\Webhooks\BankWebhookReceived;
use SistemAtc\Banks\Webhooks\Receivers\AsaasWebhookReceiver;
use SistemAtc\Banks\Webhooks\WebhookUrl;

/** Host que conhece as integrações informadas (por id). */
function resolverAsaas(BankIntegration ...$integracoes): void
{
    $porId = [];
    foreach ($integracoes as $i) {
        $porId[(string) $i->getIntegrationIdentifier()] = $i;
    }

    app()->instance(WebhookIntegrationResolver::class, new class($porId) implements WebhookIntegrationResolver
    {
        public function __construct(private array $porId) {}

        public function resolve(string $bank, string $integrationId): ?BankIntegration
        {
            return $bank === 'asaas' ? ($this->porId[$integrationId] ?? null) : null;
        }
    });
}

// ── Connector ──────────────────────────────────────────────────────────────

it('o enum Gateway resolve o Asaas', function () {
    expect(Gateway::Asaas->connector())->toBeInstanceOf(Asaas::class);
});

it('produção usa o host oficial e a chave de API da integração', function () {
    Http::preventStrayRequests();
    Http::fake(['https://api.asaas.com/v3/customers*' => Http::response(['object' => 'list', 'data' => [], 'totalCount' => 0, 'hasMore' => false, 'limit' => 10, 'offset' => 0])]);

    $i = new FakeAsaasIntegration(apiKey: '$aact_prod_xyz', sandbox: false);
    Asaas::make()->customer($i)->listCustomers(\SistemAtc\Banks\Gateways\Asaas\DTO\Request\Customer\ListCustomerRequestDTO::fromArray([]));

    Http::assertSent(fn (Request $r) => str_starts_with($r->url(), 'https://api.asaas.com/v3/customers')
        && $r->hasHeader('access_token', '$aact_prod_xyz'));
});

it('sandbox usa o host de homologação', function () {
    config()->set('banks.asaas.base_url.sandbox', 'https://api-sandbox.asaas.com/v3');

    expect(\SistemAtc\Banks\Gateways\Asaas\Support\HttpClientFactory::baseUrl(new FakeAsaasIntegration()))
        ->toBe('https://api-sandbox.asaas.com/v3');
});

it('recusa integração sem o contrato do Asaas, inativa ou sem chave', function (BankIntegration $i, string $msg) {
    expect(fn () => Asaas::make()->payment($i))->toThrow(BankAuthenticationException::class, $msg);
})->with([
    'sem contrato' => [new FakeBankIntegration(), 'precisa implementar'],
    'inativa' => [new FakeAsaasIntegration(active: false), 'inativa'],
    'sem chave' => [new FakeAsaasIntegration(apiKey: ' '), 'chave de API'],
]);

it('auth devolve a chave de API, que não expira', function () {
    $token = Asaas::make()->auth(new FakeAsaasIntegration(apiKey: 'k1'));

    expect($token->accessToken)->toBe('k1')->and($token->tokenType)->toBe('access_token');
});

// ── Webhook pela rota do SDK ───────────────────────────────────────────────

it('a URL do webhook do Asaas é única por integração', function () {
    config(['app.url' => 'https://erp.exemplo.com.br']);

    expect(WebhookUrl::for('asaas', 7, AsaasWebhookReceiver::TIPO))
        ->toBe('https://erp.exemplo.com.br/api/webhooks/banks/asaas/7/eventos');
});

it('recebe o aviso com o token da integração e entrega o evento do payload como tipo', function () {
    resolverAsaas(new FakeAsaasIntegration(webhookToken: 'segredo-7', id: 7));
    $recebidos = new ArrayObject();
    Event::listen(BankWebhookReceived::class, fn ($e) => $recebidos->append($e));

    $payload = asaasFixture('Webhook/payment');

    $this->postJson('/api/webhooks/banks/asaas/7/eventos', $payload, ['asaas-access-token' => 'segredo-7'])
        ->assertOk();

    expect($recebidos)->toHaveCount(1)
        ->and($recebidos[0]->bank)->toBe('asaas')
        ->and($recebidos[0]->integrationId)->toBe('7')
        ->and($recebidos[0]->tipo)->toBe('PAYMENT_RECEIVED')
        ->and($recebidos[0]->payload)->toEqual($payload);

    $dto = AsaasWebhookPayload::map($recebidos[0]->payload);
    expect($dto)->toBeInstanceOf(PaymentWebhookDTO::class)
        ->and($dto->getEventType())->toBe(WebhookEventAsaas::PAYMENT_RECEIVED)
        ->and($dto->payment->id)->toBe('pay_080225913252');
});

it('recusa token errado, ausente, de outra integração ou integração sem token', function (array $headers, string $id) {
    resolverAsaas(
        new FakeAsaasIntegration(webhookToken: 'segredo-7', id: 7),
        new FakeAsaasIntegration(webhookToken: 'segredo-8', id: 8),
        new FakeAsaasIntegration(webhookToken: null, id: 9),
        new FakeAsaasIntegration(webhookToken: 'segredo-10', active: false, id: 10),
    );
    Event::listen(BankWebhookReceived::class, fn () => throw new RuntimeException('não devia chegar'));

    $this->postJson("/api/webhooks/banks/asaas/{$id}/eventos", ['event' => 'PAYMENT_RECEIVED'], $headers)
        ->assertStatus(401);
})->with([
    'token errado' => [['asaas-access-token' => 'x'], '7'],
    'sem token' => [[], '7'],
    'token da 8 na 7' => [['asaas-access-token' => 'segredo-8'], '7'],
    'integração sem token' => [['asaas-access-token' => ''], '9'],
    'integração inativa' => [['asaas-access-token' => 'segredo-10'], '10'],
    'integração desconhecida' => [['asaas-access-token' => 'segredo-7'], '99'],
]);

it('responde 503 quando o host não registrou o resolver', function () {
    Event::listen(BankWebhookReceived::class, fn () => null);

    $this->postJson('/api/webhooks/banks/asaas/7/eventos', ['event' => 'PAYMENT_RECEIVED'], ['asaas-access-token' => 'segredo-7'])
        ->assertStatus(503);
});

it('só aceita o tipo eventos na URL', function () {
    resolverAsaas(new FakeAsaasIntegration(id: 7));

    $this->postJson('/api/webhooks/banks/asaas/7/cobranca', ['event' => 'PAYMENT_RECEIVED'], ['asaas-access-token' => 'token-de-teste'])
        ->assertNotFound();
});
