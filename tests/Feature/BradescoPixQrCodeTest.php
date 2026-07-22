<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Cobranca;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaEmv;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaEstatica;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaVencimento;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\CobrancaVencimentoEmv;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Devolucao;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaCobrancas;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaLocations;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaPixRecebidos;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaWebhooks;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Location;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\PixRecebido;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Webhook;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\CobrancaEstaticaMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\CobrancaImediataMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\CobrancaVencimentoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\LocationMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\PixRecebidoMethods;
use SistemAtc\Banks\Bradesco\Endpoints\PixQrCode\WebhookMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/**
 * Produto "Pix - geração de QR Code" do Bradesco — FAMÍLIA PIX
 * (host qrpix.bradesco.com.br, autorizador /v2/oauth/token).
 */
function authedPixQrCode(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

/** Fake dos DOIS autorizadores — o factory autentica antes de qualquer chamada. */
function fakeTokensPixQrCode(array $routes): void
{
    Http::fake([
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        ...$routes,
    ]);
}

function cobrancaImediataMethods(): CobrancaImediataMethods
{
    $i = authedPixQrCode();

    return new CobrancaImediataMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);
}

// ---------------------------------------------------------------- cobrança imediata

it('cria cobranca imediata sem txid (POST /v2/cob)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob' => Http::response([
            'txid' => '0098952a70994d49aa32d6d47a9bf935',
            'location' => 'qrpix.bradesco.com.br/qr/v2/cff1cf8c',
            'revisao' => 0,
            'calendario' => ['criacao' => '2024-04-19T19:45:04.970Z', 'expiracao' => 100000],
            'status' => 'ATIVA',
            'devedor' => ['cpf' => '12345678909', 'nome' => 'Rogerio'],
            'valor' => ['original' => '1000.35', 'modalidadeAlteracao' => 0],
            'chave' => 'b3bdd4e4-7cdc-41c1-bd70-7842d987d79f',
            'infoAdicionais' => [['nome' => 'Campo1', 'valor' => 'Info 1']],
            'loc' => ['id' => 28754813, 'location' => 'qrpix.bradesco.com.br/qr/v2/cff1cf8c', 'tipoCob' => 'cob'],
            'pixCopiaECola' => '00020101021226880014BR.GOV.BCB.PIX',
        ], 201),
    ]);

    $cob = cobrancaImediataMethods()->criar(['valor' => ['original' => '1000.35']]);

    expect($cob)->toBeInstanceOf(Cobranca::class)
        ->and($cob->txid)->toBe('0098952a70994d49aa32d6d47a9bf935')
        ->and($cob->status)->toBe('ATIVA')
        ->and($cob->calendario?->expiracao)->toBe(100000)
        ->and($cob->devedor?->nome)->toBe('Rogerio')
        ->and($cob->valor?->original)->toBe('1000.35')
        ->and($cob->loc?->id)->toBe(28754813)
        ->and($cob->infoAdicionais[0]->nome)->toBe('Campo1');
});

it('cria cobranca imediata com txid (PUT /v2/cob/{txid})', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob/APIPix*' => Http::response([
            'txid' => 'APIPixBradesco000000000000000000003',
            'status' => 'ATIVA',
            'revisao' => 0,
            'calendario' => ['criacao' => '2021-11-11T01:21:25.027Z', 'expiracao' => 3600],
            'valor' => [
                'original' => '10.00',
                'modalidadeAlteracao' => 0,
                'retirada' => ['troco' => [
                    'valor' => '5.00',
                    'modalidadeAlteracao' => 0,
                    'modalidadeAgente' => 'AGTEC',
                    'prestadorDoServicoDeSaque' => '60746948',
                ]],
            ],
        ], 201),
    ]);

    $cob = cobrancaImediataMethods()->criarComTxid('APIPixBradesco000000000000000000003', [
        'calendario' => ['expiracao' => 3600],
        'valor' => ['original' => '10.00'],
    ]);

    expect($cob->txid)->toBe('APIPixBradesco000000000000000000003')
        ->and($cob->valor?->retirada?->troco?->modalidadeAgente)->toBe('AGTEC')
        ->and($cob->valor?->retirada?->troco?->prestadorDoServicoDeSaque)->toBe('60746948');
});

it('revisa cobranca imediata (PATCH /v2/cob/{txid}) — expiracao string vira int', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob/7978*' => Http::response([
            'status' => 'REMOVIDA_PELO_USUARIO_RECEBEDOR',
            'calendario' => ['criacao' => '2020-09-09T20:15:00.358Z', 'expiracao' => '5350'],
            'txid' => '7978c0c9-7ea8-47e7-8e88-49634473c11',
            'revisao' => 1,
        ]),
    ]);

    $cob = cobrancaImediataMethods()->revisar('7978c0c9-7ea8-47e7-8e88-49634473c11', [
        'status' => 'REMOVIDA_PELO_USUARIO_RECEBEDOR',
    ]);

    expect($cob->status)->toBe('REMOVIDA_PELO_USUARIO_RECEBEDOR')
        ->and($cob->calendario?->expiracao)->toBe(5350)
        ->and($cob->revisao)->toBe(1);
});

it('consulta cobranca imediata com revisao (GET /v2/cob/{txid}) e hidrata os pix recebidos', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob/APIPix*' => Http::response([
            'status' => 'CONCLUIDA',
            'txid' => 'APIPixBradesco000000000000000000003',
            'codCpfCnpj' => 12345678,
            'codFilial' => 1,
            'pix' => [[
                'endToEndId' => 'E12345678202009091221kkkkkkkkkkk',
                'valor' => '110.00',
                'horario' => '2020-09-09T20:15:00.358Z',
                'devolucoes' => [[
                    'id' => '123ABC',
                    'rtrId' => 'Dxxxxxxxx202009091221kkkkkkkkkkk',
                    'valor' => '10.00',
                    'horario' => ['solicitacao' => '2020-09-09T20:15:00.358Z'],
                    'status' => 'EM_PROCESSAMENTO',
                ]],
            ]],
        ]),
    ]);

    $cob = cobrancaImediataMethods()->consultar('APIPixBradesco000000000000000000003', revisao: 1);

    expect($cob->status)->toBe('CONCLUIDA')
        ->and($cob->codCpfCnpj)->toBe(12345678)
        ->and($cob->pix[0])->toBeInstanceOf(PixRecebido::class)
        ->and($cob->pix[0]->endToEndId)->toBe('E12345678202009091221kkkkkkkkkkk')
        ->and($cob->pix[0]->devolucoes[0]->status)->toBe('EM_PROCESSAMENTO')
        ->and($cob->pix[0]->devolucoes[0]->horario?->solicitacao)->toBe('2020-09-09T20:15:00.358Z');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'revisao=1'));
});

it('lista cobrancas imediatas (GET /v2/cob)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob?*' => Http::response([
            'parametros' => [
                'inicio' => '2020-04-01T00:00:00.000Z',
                'fim' => '2020-04-02T10:00:00.000Z',
                'paginacao' => [
                    'paginaAtual' => 0,
                    'itensPorPagina' => 100,
                    'quantidadeDePaginas' => 1,
                    'quantidadeTotalDeItens' => 2,
                ],
            ],
            'cobs' => [
                ['status' => 'ATIVA', 'txid' => 'A', 'valor' => ['original' => '567.89']],
                ['status' => 'CONCLUIDA', 'txid' => 'B', 'valor' => ['original' => '100.00']],
            ],
        ]),
    ]);

    $lista = cobrancaImediataMethods()->listar([
        'inicio' => '2020-04-01T00:00:00.000Z',
        'fim' => '2020-04-02T10:00:00.000Z',
        'paginacao.itensPorPagina' => 100,
    ]);

    expect($lista)->toBeInstanceOf(ListaCobrancas::class)
        ->and($lista->cobs)->toHaveCount(2)
        ->and($lista->cobs[1]->status)->toBe('CONCLUIDA')
        ->and($lista->parametros?->paginacao?->quantidadeTotalDeItens)->toBe(2);

    Http::assertSent(fn ($r) => str_contains($r->url(), 'paginacao.itensPorPagina=100'));
});

it('lista cobrancas por chave pix (GET /v1/cob/chavepix)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v1/cob/chavepix*' => Http::response([
            'parametros' => ['status' => 'ATIVA'],
            'cobs' => [['txid' => 'A', 'chave' => 'chave@ex.com']],
        ]),
    ]);

    $lista = cobrancaImediataMethods()->listarPorChave('chave@ex.com', [
        'inicio' => '2020-04-01T00:00:00.000Z',
        'fim' => '2020-04-02T10:00:00.000Z',
    ]);

    expect($lista->cobs[0]->chave)->toBe('chave@ex.com');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'chave=chave%40ex.com'));
});

it('cria cobranca imediata EMV com txid (PUT /v2/cob-emv/{txid})', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob-emv/*' => Http::response([
            'cob' => [
                'txid' => 'APIPixBradesco000000000000000000008',
                'status' => 'ATIVA',
                'valor' => ['original' => '10.00'],
                'loc' => ['id' => 180399, 'tipoCob' => 'cob'],
            ],
            'emv' => '00020101021226880014BR.GOV.BCB.PIX',
            'base64' => 'iVBORw0KGgo=',
        ], 201),
    ]);

    $emv = cobrancaImediataMethods()->criarEmvComTxid('APIPixBradesco000000000000000000008', [
        'valor' => ['original' => '10.00'],
    ]);

    expect($emv)->toBeInstanceOf(CobrancaEmv::class)
        ->and($emv->base64)->toBe('iVBORw0KGgo=')
        ->and($emv->cob?->txid)->toBe('APIPixBradesco000000000000000000008')
        ->and($emv->cob?->loc?->id)->toBe(180399);
});

it('cria cobranca imediata EMV sem txid (POST /v2/cob-emv)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob-emv' => Http::response([
            'cob' => ['txid' => 'GERADO', 'status' => 'ATIVA'],
            'emv' => 'EMVPAYLOAD',
        ], 201),
    ]);

    $emv = cobrancaImediataMethods()->criarEmv(['valor' => ['original' => '1.00']]);

    expect($emv->emv)->toBe('EMVPAYLOAD')->and($emv->cob?->txid)->toBe('GERADO');
});

// ------------------------------------------------------------ cobrança com vencimento

it('cria cobranca com vencimento (PUT /v2/cobv/{txid}) com multa, juros e desconto', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cobv/fda9*' => Http::response([
            'txid' => 'fda9460fe04e4f129b72863ae57ee22f',
            'status' => 'ATIVA',
            'revisao' => 0,
            'calendario' => [
                'criacao' => '2020-09-09T20:15:00.358Z',
                'dataDeVencimento' => '2020-12-31',
                'validadeAposVencimento' => 30,
            ],
            'devedor' => ['cpf' => '12345678909', 'nome' => 'Francisco', 'cidade' => 'Osasco', 'uf' => 'SP'],
            'recebedor' => ['nome' => 'Empresa', 'nomeFantasia' => 'Fantasia', 'uf' => 'SP'],
            'valor' => [
                'original' => '123.45',
                'multa' => ['modalidade' => 2, 'valorPerc' => '15.00'],
                'juros' => ['modalidade' => 2, 'valorPerc' => '2.00'],
                'abatimento' => ['modalidade' => 1, 'valorPerc' => '3.00'],
                'desconto' => [
                    'modalidade' => 1,
                    'descontoDataFixa' => [['data' => '2020-11-30', 'valorPerc' => '5.00']],
                ],
            ],
            'chave' => 'chave@ex.com',
        ], 201),
    ]);

    $i = authedPixQrCode();
    $m = new CobrancaVencimentoMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $cobv = $m->criar('fda9460fe04e4f129b72863ae57ee22f', ['valor' => ['original' => '123.45']]);

    expect($cobv)->toBeInstanceOf(CobrancaVencimento::class)
        ->and($cobv->calendario?->dataDeVencimento)->toBe('2020-12-31')
        ->and($cobv->calendario?->validadeAposVencimento)->toBe(30)
        ->and($cobv->devedor?->cidade)->toBe('Osasco')
        ->and($cobv->recebedor?->nomeFantasia)->toBe('Fantasia')
        ->and($cobv->valor?->multa?->valorPerc)->toBe('15.00')
        ->and($cobv->valor?->juros?->modalidade)->toBe(2)
        ->and($cobv->valor?->abatimento?->valorPerc)->toBe('3.00')
        ->and($cobv->valor?->desconto?->descontoDataFixa[0]->valorPerc)->toBe('5.00');
});

it('revisa, consulta e lista cobrancas com vencimento (PATCH/GET /v2/cobv)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cobv?*' => Http::response([
            'parametros' => ['inicio' => 'x', 'loteCobVId' => '55'],
            'cobs' => [['txid' => 'A', 'status' => 'ATIVA']],
        ]),
        'qrpix.bradesco.com.br/v2/cobv/*' => Http::response(['txid' => 'A', 'status' => 'ATIVA', 'revisao' => 3]),
    ]);

    $i = authedPixQrCode();
    $m = new CobrancaVencimentoMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    expect($m->revisar('A', ['status' => 'ATIVA'])->revisao)->toBe(3)
        ->and($m->consultar('A')->txid)->toBe('A')
        ->and($m->listar(['inicio' => 'a', 'fim' => 'b'])->parametros?->loteCobVId)->toBe('55');
});

it('cria cobranca com vencimento EMV (PUT /v2/cobv-emv/{txid})', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cobv-emv/*' => Http::response([
            'emv' => 'EMVCOBV',
            'base64' => 'QkFTRTY0',
            'cobv' => ['txid' => 'A', 'status' => 'ATIVA', 'valor' => ['original' => '9.99']],
        ], 201),
    ]);

    $i = authedPixQrCode();
    $m = new CobrancaVencimentoMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $emv = $m->criarEmv('A', ['valor' => ['original' => '9.99']]);

    expect($emv)->toBeInstanceOf(CobrancaVencimentoEmv::class)
        ->and($emv->emv)->toBe('EMVCOBV')
        ->and($emv->cobv?->valor?->original)->toBe('9.99');
});

// ------------------------------------------------------------------ cobrança estática

it('gera cobranca estatica (POST /v1/cobe)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v1/cobe' => Http::response([
            'txid' => 'ESTATICA001',
            'valor' => '25.00',
            'chave' => 'chave@ex.com',
            'solicitacaoPagador' => 'Pague aqui',
            'pixCopiaECola' => '00020126...',
            'base64' => 'iVBORw0KGgo=',
        ], 201),
    ]);

    $i = authedPixQrCode();
    $m = new CobrancaEstaticaMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $cobe = $m->criar(['txid' => 'ESTATICA001', 'valor' => '25.00', 'chave' => 'chave@ex.com']);

    expect($cobe)->toBeInstanceOf(CobrancaEstatica::class)
        ->and($cobe->txid)->toBe('ESTATICA001')
        ->and($cobe->base64)->toBe('iVBORw0KGgo=');
});

// ------------------------------------------------------------------------- location

it('cria, consulta, lista e desvincula location (/v2/loc)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/loc?*' => Http::response([
            'parametros' => ['tipoCob' => 'cobv', 'txIdPresente' => true],
            'loc' => [[
                'id' => 7716,
                'txid' => 'fda9460fe04e4f129b72863ae57ee22f',
                'location' => 'pix.example.com/qr/v2/cobv/2353c790',
                'tipoCob' => 'cobv',
            ]],
        ]),
        'qrpix.bradesco.com.br/v2/loc/*/txid' => Http::response([
            'id' => 2316, 'location' => 'pix.example.com/qr/v2/a8534e27', 'tipoCob' => 'cob',
        ]),
        'qrpix.bradesco.com.br/v2/loc/*' => Http::response(['id' => 7716, 'tipoCob' => 'cobv']),
        'qrpix.bradesco.com.br/v2/loc' => Http::response(['id' => 7716, 'tipoCob' => 'cob'], 201),
    ]);

    $i = authedPixQrCode();
    $m = new LocationMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $criada = $m->criar(['tipoCob' => 'cob']);
    $lista = $m->listar(['inicio' => 'a', 'fim' => 'b', 'tipoCob' => 'cobv']);

    expect($criada)->toBeInstanceOf(Location::class)
        ->and($criada->id)->toBe(7716)
        ->and($m->consultar(7716)->tipoCob)->toBe('cobv')
        ->and($m->desvincularTxid(2316)->id)->toBe(2316)
        ->and($lista)->toBeInstanceOf(ListaLocations::class)
        ->and($lista->loc[0]->txid)->toBe('fda9460fe04e4f129b72863ae57ee22f')
        ->and($lista->parametros?->txIdPresente)->toBeTrue();
});

// -------------------------------------------------------------------- pix recebidos

it('lista e consulta pix recebidos (/v2/pix)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/pix?*' => Http::response([
            'parametros' => ['devolucaoPresente' => false, 'paginacao' => ['paginaAtual' => 0]],
            'pix' => [
                ['endToEndId' => 'E607469482021', 'txid' => 'T1', 'valor' => '300.00', 'devolucoes' => []],
                ['endToEndId' => 'E607469482022', 'txid' => 'T2', 'valor' => '116.63', 'devolucoes' => []],
            ],
        ]),
        'qrpix.bradesco.com.br/v2/pix/E607469482021' => Http::response([
            'endToEndId' => 'E607469482021',
            'txid' => 'T1',
            'valor' => '300.00',
            'componentesValor' => ['original' => ['valor' => '290.00'], 'troco' => ['valor' => '10.00']],
            'pagador' => ['cpf' => '12345678909', 'nome' => 'Pagador'],
            'devolucoes' => [],
        ]),
    ]);

    $i = authedPixQrCode();
    $m = new PixRecebidoMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $lista = $m->listar(['inicio' => 'a', 'fim' => 'b']);
    $pix = $m->consultar('E607469482021');

    expect($lista)->toBeInstanceOf(ListaPixRecebidos::class)
        ->and($lista->pix)->toHaveCount(2)
        ->and($lista->parametros?->devolucaoPresente)->toBeFalse()
        ->and($pix)->toBeInstanceOf(PixRecebido::class)
        ->and($pix->componentesValor?->original?->valor)->toBe('290.00')
        ->and($pix->componentesValor?->troco?->valor)->toBe('10.00')
        ->and($pix->pagador?->nome)->toBe('Pagador');
});

it('solicita e consulta devolucao de pix (/v2/pix/{e2eid}/devolucao/{id})', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/pix/*/devolucao/*' => Http::response([
            'id' => '123456',
            'rtrId' => 'D12345678202009091000abcde123456',
            'valor' => '7.89',
            'horario' => ['solicitacao' => '2020-09-11T15:25:59.411Z'],
            'status' => 'EM_PROCESSAMENTO',
        ], 201),
    ]);

    $i = authedPixQrCode();
    $m = new PixRecebidoMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $dev = $m->solicitarDevolucao('E60746948202106181235G3995xmn8nw', '123456', [
        'valor' => '7.89',
        'natureza' => 'ORIGINAL',
        'descricao' => 'Devolução parcial',
    ]);

    expect($dev)->toBeInstanceOf(Devolucao::class)
        ->and($dev->rtrId)->toBe('D12345678202009091000abcde123456')
        ->and($dev->horario?->solicitacao)->toBe('2020-09-11T15:25:59.411Z')
        ->and($m->consultarDevolucao('E60746948202106181235G3995xmn8nw', '123456')->status)
        ->toBe('EM_PROCESSAMENTO');
});

// -------------------------------------------------------------------------- webhook

it('configura, consulta, lista e exclui webhook (/v2/webhook)', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/webhook?*' => Http::response([
            'parametros' => [
                'inicio' => '2020-04-01T00:00:00Z',
                'webhooks' => [['webhookUrl' => 'https://ex.com/hook', 'chave' => 'chave@ex.com']],
            ],
        ]),
        'qrpix.bradesco.com.br/v2/webhook/*' => Http::response([
            'webhookUrl' => 'https://ex.com/hook',
            'chave' => 'chave@ex.com',
            'criacao' => '2020-04-01T00:00:00Z',
        ]),
    ]);

    $i = authedPixQrCode();
    $m = new WebhookMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_PIX), $i);

    $hook = $m->configurar('chave@ex.com', ['webhookUrl' => 'https://ex.com/hook']);
    $lista = $m->listar(['inicio' => '2020-04-01T00:00:00Z']);

    expect($hook)->toBeInstanceOf(Webhook::class)
        ->and($hook->webhookUrl)->toBe('https://ex.com/hook')
        ->and($m->consultar('chave@ex.com')->chave)->toBe('chave@ex.com')
        ->and($m->excluir('chave@ex.com'))->toBeTrue()
        ->and($lista)->toBeInstanceOf(ListaWebhooks::class)
        // a spec aninha `webhooks` dentro de `parametros`; o método normaliza.
        ->and($lista->webhooks[0]->chave)->toBe('chave@ex.com');
});

// -------------------------------------------------------------------- família / token

it('usa a familia PIX — host qrpix e autorizador /v2/oauth/token', function () {
    fakeTokensPixQrCode([
        'qrpix.bradesco.com.br/v2/cob' => Http::response(['txid' => 'X'], 201),
    ]);

    cobrancaImediataMethods()->criar(['valor' => ['original' => '1.00']]);

    Http::assertSent(fn ($r) => str_contains($r->url(), 'qrpix.bradesco.com.br/v2/oauth/token'));
    Http::assertSent(fn ($r) => str_contains($r->url(), 'qrpix.bradesco.com.br/v2/cob'));
    Http::assertNotSent(fn ($r) => str_contains($r->url(), 'openapi.bradesco.com.br'));
});

it('serializa o DTO de volta em camelCase (toArray)', function () {
    $cob = Cobranca::fromArray([
        'txid' => 'A',
        'pixCopiaECola' => '000201',
        'calendario' => ['expiracao' => 3600],
        'infoAdicionais' => [['nome' => 'n', 'valor' => 'v']],
    ]);

    $array = $cob->toArray();

    expect($array)->toHaveKey('pixCopiaECola')
        ->and($array['calendario'])->toBe(['expiracao' => 3600])
        ->and($array['infoAdicionais'][0])->toBe(['nome' => 'n', 'valor' => 'v']);
});
