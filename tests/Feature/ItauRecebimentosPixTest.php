<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Cobranca;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\CobrancaList;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Devolucao;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Location;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Pix;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\PixList;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Webhook;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\CobrancaImediataMethods;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\CobrancaVencimentoMethods;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\LocationMethods;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\PixRecebidoMethods;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\WebhookMethods;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedRecebimentosPix(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('cria cobranca imediata com txid (PUT /regulatorio-pix/v2/cob/{txid})', function () {
    Http::fake([
        '*/regulatorio-pix/v2/cob/*' => Http::response([
            'calendario' => ['criacao' => '2024-04-10T11:00:00Z', 'expiracao' => 3600],
            'txid' => 'abc123def456ghi789jkl012mno',
            'revisao' => 0,
            'status' => 'ATIVA',
            'chave' => 'minha-chave@itau.com',
            'valor' => ['original' => '110.00', 'modalidadeAlteracao' => 0],
            'devedor' => ['cpf' => '12345678909', 'nome' => 'Fulano'],
            'loc' => ['id' => 789, 'tipoCob' => 'cob'],
            'pixCopiaECola' => '00020101...6304ABCD',
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new CobrancaImediataMethods(HttpClientFactory::make($i), $i);
    $res = $m->criarComTxid('abc123def456ghi789jkl012mno', ['valor' => ['original' => '110.00']]);

    expect($res)->toBeInstanceOf(Cobranca::class)
        ->and($res->txid)->toBe('abc123def456ghi789jkl012mno')
        ->and($res->status)->toBe('ATIVA')
        ->and($res->valor?->original)->toBe('110.00')
        ->and($res->devedor?->nome)->toBe('Fulano')
        ->and($res->loc?->id)->toBe(789)
        ->and($res->calendario?->expiracao)->toBe(3600);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/regulatorio-pix/v2/cob/abc123')
        && $r->method() === 'PUT'
        && $r->hasHeader('x-itau-apikey', 'cli')
        && $r->hasHeader('x-itau-correlationID')
        && $r->hasHeader('Authorization', 'Bearer TOK'));
});

it('lista cobrancas imediatas desembrulhando parametros{paginacao} + cobs[]', function () {
    Http::fake([
        '*/regulatorio-pix/v2/cob*' => Http::response([
            'parametros' => ['paginacao' => ['paginaAtual' => 0, 'quantidadeTotalDeItens' => 2]],
            'cobs' => [
                ['txid' => 't1', 'status' => 'ATIVA'],
                ['txid' => 't2', 'status' => 'CONCLUIDA'],
            ],
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new CobrancaImediataMethods(HttpClientFactory::make($i), $i);
    $list = $m->listar(['inicio' => '2024-04-01T00:00:00Z', 'fim' => '2024-04-30T23:59:59Z']);

    expect($list)->toBeInstanceOf(CobrancaList::class)
        ->and($list->cobs)->toHaveCount(2)
        ->and($list->cobs[0]->txid)->toBe('t1')
        ->and($list->parametros?->paginacao?->quantidadeTotalDeItens)->toBe(2);
});

it('emite cobranca com vencimento (PUT /regulatorio-pix/v2/cobv/{txid}) com encargos', function () {
    Http::fake([
        '*/regulatorio-pix/v2/cobv/*' => Http::response([
            'txid' => 'cobv000000000000000000000001',
            'status' => 'ATIVA',
            'calendario' => ['dataDeVencimento' => '2024-12-31', 'validadeAposVencimento' => 30],
            'valor' => [
                'original' => '200.00',
                'multa' => ['modalidade' => 2, 'valorPerc' => '2.00'],
                'juros' => ['modalidade' => 2, 'valorPerc' => '1.00'],
                'desconto' => ['modalidade' => 1, 'descontoDataFixa' => [['data' => '2024-12-20', 'valorPerc' => '10.00']]],
            ],
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new CobrancaVencimentoMethods(HttpClientFactory::make($i), $i);
    $res = $m->criar('cobv000000000000000000000001', ['valor' => ['original' => '200.00']]);

    expect($res)->toBeInstanceOf(Cobranca::class)
        ->and($res->calendario?->dataDeVencimento)->toBe('2024-12-31')
        ->and($res->valor?->multa?->valorPerc)->toBe('2.00')
        ->and($res->valor?->desconto?->descontoDataFixa)->toHaveCount(1);
});

it('consulta Pix recebido (GET /pix/{e2eid}) com componentesValor e devolucoes', function () {
    Http::fake([
        '*/regulatorio-pix/v2/pix/*' => Http::response([
            'endToEndId' => 'E12345678202009091221kkkkkkkkkkk',
            'txid' => '7978c0c97ea847e78e8849634473c1f1',
            'valor' => '110.00',
            'horario' => '2020-01-01T00:00:00Z',
            'chave' => 'b53609a5-56a2-4e0d-bd04-0d6b296c4ea6',
            'componentesValor' => [
                'original' => ['valor' => '10.0'],
                'desconto' => ['valor_desconto_documento_cobranca_pix' => '90.0'],
            ],
            'devolucoes' => [
                ['id' => '123ABC', 'rtrId' => 'D12345678202009091221abcdf098765', 'valor' => '10.00', 'status' => 'DEVOLVIDO', 'horario' => ['solicitacao' => '2020-01-01T00:00:00Z']],
            ],
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new PixRecebidoMethods(HttpClientFactory::make($i), $i);
    $res = $m->consultar('E12345678202009091221kkkkkkkkkkk');

    expect($res)->toBeInstanceOf(Pix::class)
        ->and($res->valor)->toBe('110.00')
        ->and($res->componentesValor?->original?->valor)->toBe('10.0')
        ->and($res->componentesValor?->desconto?->valorDescontoDocumentoCobrancaPix)->toBe('90.0')
        ->and($res->devolucoes)->toHaveCount(1)
        ->and($res->devolucoes[0]->status)->toBe('DEVOLVIDO')
        ->and($res->devolucoes[0]->horario?->solicitacao)->toBe('2020-01-01T00:00:00Z');
});

it('lista Pix recebidos (GET /pix) em pix[]', function () {
    Http::fake([
        '*/regulatorio-pix/v2/pix*' => Http::response([
            'parametros' => ['paginacao' => ['paginaAtual' => 0]],
            'pix' => [
                ['endToEndId' => 'E1', 'valor' => '10.00'],
                ['endToEndId' => 'E2', 'valor' => '20.00'],
            ],
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new PixRecebidoMethods(HttpClientFactory::make($i), $i);
    $list = $m->listar(['inicio' => '2024-01-01T00:00:00Z', 'fim' => '2024-01-31T00:00:00Z']);

    expect($list)->toBeInstanceOf(PixList::class)
        ->and($list->pix)->toHaveCount(2)
        ->and($list->pix[1]->endToEndId)->toBe('E2');
});

it('solicita devolucao (PUT /pix/{e2eid}/devolucao/{id}) retornando status final', function () {
    Http::fake([
        '*/regulatorio-pix/v2/pix/*/devolucao/*' => Http::response([
            'id' => 'dev001',
            'rtrId' => 'D12345678202009091221abcdf098765',
            'valor' => '50.00',
            'status' => 'EM_PROCESSAMENTO',
            'natureza' => 'ORIGINAL',
            'horario' => ['solicitacao' => '2024-04-10T12:00:00Z'],
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new PixRecebidoMethods(HttpClientFactory::make($i), $i);
    $res = $m->solicitarDevolucao('E12345678202009091221kkkkkkkkkkk', 'dev001', ['valor' => '50.00']);

    expect($res)->toBeInstanceOf(Devolucao::class)
        ->and($res->id)->toBe('dev001')
        ->and($res->status)->toBe('EM_PROCESSAMENTO')
        ->and($res->valor)->toBe('50.00');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/pix/E12345678202009091221kkkkkkkkkkk/devolucao/dev001')
        && $r->method() === 'PUT');
});

it('cadastra webhook para uma chave (PUT /webhook/{chave})', function () {
    Http::fake([
        '*/regulatorio-pix/v2/webhook/*' => Http::response([
            'webhookUrl' => 'https://exemplo.com/itau',
            'chave' => 'minha-chave@itau.com',
            'criacao' => '2024-04-10T12:00:00Z',
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new WebhookMethods(HttpClientFactory::make($i), $i);
    $res = $m->cadastrar('minha-chave@itau.com', ['webhookUrl' => 'https://exemplo.com/itau']);

    expect($res)->toBeInstanceOf(Webhook::class)
        ->and($res->webhookUrl)->toBe('https://exemplo.com/itau')
        ->and($res->chave)->toBe('minha-chave@itau.com');
});

it('cria location (POST /loc)', function () {
    Http::fake([
        '*/regulatorio-pix/v2/loc' => Http::response([
            'id' => 807,
            'location' => 'pix.example.com/qr/v2/2353c790',
            'tipoCob' => 'cob',
            'criacao' => '2024-04-10T12:00:00Z',
        ]),
    ]);

    $i = authedRecebimentosPix();
    $m = new LocationMethods(HttpClientFactory::make($i), $i);
    $res = $m->criar(['tipoCob' => 'cob']);

    expect($res)->toBeInstanceOf(Location::class)
        ->and($res->id)->toBe(807)
        ->and($res->tipoCob)->toBe('cob');
});
