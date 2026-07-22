<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\Endpoints\Cobranca\CobrancaConsultaMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Cobranca\CobrancaMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Cobranca\CobrancaSplitMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Cobranca\CobrancaWebhookMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/**
 * Cobrança Bradesco — a fiação no connector (Bank::Bradesco->cobranca()) ainda
 * não existe, então instanciamos as classes de método direto via
 * HttpClientFactory. Todas as operações são da família open_api.
 */
function authedCobranca(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

/** @return array<string, mixed> */
function tokenFakesCobranca(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

function cobrancaMethods(): CobrancaMethods
{
    $i = authedCobranca();

    return new CobrancaMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API), $i);
}

function cobrancaConsultaMethods(): CobrancaConsultaMethods
{
    $i = authedCobranca();

    return new CobrancaConsultaMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API), $i);
}

function cobrancaSplitMethods(): CobrancaSplitMethods
{
    $i = authedCobranca();

    return new CobrancaSplitMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API), $i);
}

function cobrancaWebhookMethods(): CobrancaWebhookMethods
{
    $i = authedCobranca();

    return new CobrancaWebhookMethods(HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API), $i);
}

it('registra boleto (POST /boleto/cobranca-registro/v1/cobranca)', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-registro/v1/cobranca' => Http::response([
            'idProduto' => 9,
            'negociacao' => 38610041000,
            'nuTituloGerado' => 41970000001,
            'linhaDigitavel' => '23791234500000010009999900000000000000000000',
            'cdBarras' => '23799999900000010000123450000000000000000000',
            'nomePagador' => 'FULANO DE TAL',
            'vlTitulo' => 1000,
            'dtVencimento' => '01.02.2026',
            'cpfcnpjBeneficiário' => '11222333000181',
            'dtInstrucaoProtestoNegativação' => '10.02.2026',
        ]),
    ]);

    $boleto = cobrancaMethods()->registrar([
        'nuCPFCNPJ' => '11222333',
        'filialCPFCNPJ' => '0001',
        'ctrlCPFCNPJ' => '81',
        'idProduto' => '09',
        'nuNegociacao' => '38610041000',
        'vlNominalTitulo' => '1000',
    ]);

    expect($boleto->nuTituloGerado)->toBe(41970000001)
        ->and($boleto->linhaDigitavel)->toBe('23791234500000010009999900000000000000000000')
        ->and($boleto->vlTitulo)->toBe(1000)
        // campos com acento no JSON viram propriedade ASCII via #[JsonKey].
        ->and($boleto->cpfcnpjBeneficiario)->toBe('11222333000181')
        ->and($boleto->dtInstrucaoProtestoNegativacao)->toBe('10.02.2026');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/cobranca-registro/v1/cobranca')
        && $r->method() === 'POST');
});

it('altera boleto com verbo PUT (PUT /boleto/cobranca-altera/v1/alterar)', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-altera/v1/alterar' => Http::response([
            'status' => 0,
            'transacao' => 'CBTTIAG3',
            'mensagem' => 'Operação realizada com sucesso.',
            'causa' => 'CBTT0000 - ALTERACAO EFETUADA',
        ]),
    ]);

    $r = cobrancaMethods()->alterar([
        'cpfCnpj' => ['cpfCnpj' => '11222333', 'filial' => '0001', 'controle' => '81'],
        'produto' => 9,
        'negociacao' => 38610041000,
        'nossoNumero' => 41970000001,
        'dadosTitulo' => ['vencimento' => ['dataVencimento' => 20260201]],
    ]);

    expect($r->status)->toBe(0)->and($r->transacao)->toBe('CBTTIAG3');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/cobranca-altera/v1/alterar')
        && $r->method() === 'PUT');
});

it('baixa título desembrulhando o bloco dados (POST /boleto/cobranca-baixa/v1/baixar)', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-baixa/v1/baixar' => Http::response([
            'status' => 0,
            'transacao' => 'CBTTIAG3',
            'mensagem' => 'Operação realizada com sucesso.',
            'causa' => 'CBTT0000 - BAIXA SOLICITADA',
            'dados' => [
                'dataHoraSolicitacao' => '2026-02-01T10:00:00',
                'status' => 61,
                'statusAnterior' => 1,
            ],
        ]),
    ]);

    $r = cobrancaMethods()->baixar([
        'cpfCnpj' => ['cpfCnpj' => 11222333, 'filial' => 1, 'controle' => 81],
        'produto' => 9,
        'negociacao' => 38610041000,
        'nossoNumero' => 41970000001,
        'sequencia' => 0,
        'codigoBaixa' => 61,
    ]);

    expect($r->dados?->status)->toBe(61)
        ->and($r->dados?->statusAnterior)->toBe(1)
        ->and($r->dados?->dataHoraSolicitacao)->toBe('2026-02-01T10:00:00');
});

it('protesta/negativa título (POST /boleto/cobranca-protesto-negativacao/v1/executar)', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-protesto-negativacao/v1/executar' => Http::response([
            'status' => 0,
            'transacao' => 'CBTTIAG3',
            'mensagem' => 'Operação realizada com sucesso.',
            'dataHoraSolicitacao' => '2026-02-01T10:00:00',
            'situacaoAtual' => 3,
            'situacaoAnterior' => 1,
        ]),
    ]);

    $r = cobrancaMethods()->protestarOuNegativar([
        'cpfCnpj' => ['cpfCnpj' => '11222333', 'filial' => '0001', 'controle' => '81'],
        'codigoProduto' => '09',
        'contaProduto' => '38610041000',
        'nossoNumero' => '41970000001',
        'codigoFuncao' => '1',
        'parmFuncao' => 'P',
    ]);

    expect($r->situacaoAtual)->toBe(3)->and($r->situacaoAnterior)->toBe(1);
});

it('consulta título específico com bloco titulo e pessoas aninhadas', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-consulta/v1/consultar' => Http::response([
            'status' => 0,
            'transacao' => 'CBTTIAG3',
            'quantidadeMensagens' => 2,
            'lista' => [
                ['mensagem' => 'NAO RECEBER APOS O VENCIMENTO'],
                ['mensagem' => 'PAGAVEL EM QUALQUER BANCO'],
            ],
            'titulo' => [
                'linhaDig' => '23791234500000010009999900000000000000000000',
                'codBarras' => '23799999900000010000123450000000000000000000',
                'dataVenctoBol' => '01.02.2026',
                'valMoeda' => 1000,
                'cedente' => ['nome' => 'SOLDIERS NUTRITION', 'uf' => 'SP', 'cep' => 4571010],
                'sacado' => ['nome' => 'FULANO DE TAL', 'cidade' => 'SAO PAULO'],
                'baixa' => ['codigo' => 61, 'descricao' => 'BAIXA POR SOLICITACAO', 'data' => 20260210],
            ],
        ]),
    ]);

    $r = cobrancaConsultaMethods()->consultarTitulo([
        'cpfCnpj' => ['cpfCnpj' => '11222333', 'filial' => '0001', 'controle' => '81'],
        'produto' => '09',
        'negociacao' => '38610041000',
        'nossoNumero' => '41970000001',
        'sequencia' => '0',
    ]);

    expect($r->titulo?->linhaDig)->toBe('23791234500000010009999900000000000000000000')
        ->and($r->titulo?->cedente?->nome)->toBe('SOLDIERS NUTRITION')
        // cep chega como inteiro na spec e é normalizado pra string no DTO.
        ->and($r->titulo?->cedente?->cep)->toBe('4571010')
        ->and($r->titulo?->sacado?->cidade)->toBe('SAO PAULO')
        ->and($r->titulo?->baixa?->codigo)->toBe(61)
        ->and($r->lista)->toHaveCount(2)
        ->and($r->lista[0]->mensagem)->toBe('NAO RECEBER APOS O VENCIMENTO');
});

it('emite 2a via montando o payload minimo da consulta', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-consulta/v1/consultar' => Http::response([
            'status' => 0,
            'titulo' => ['linhaDig' => '23791234500000010009999900000000000000000000'],
        ]),
    ]);

    $r = cobrancaConsultaMethods()->segundaVia(
        cpfCnpj: ['cpfCnpj' => '11222333', 'filial' => '0001', 'controle' => '81'],
        produto: '09',
        negociacao: '38610041000',
        nossoNumero: '41970000001',
    );

    expect($r->titulo?->linhaDig)->toBe('23791234500000010009999900000000000000000000');

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), '/boleto/cobranca-consulta/v1/consultar')) {
            return false;
        }

        return $request['sequencia'] === '0'
            && $request['nossoNumero'] === '41970000001'
            && $request['cpfCnpj']['filial'] === '0001';
    });
});

it('lista títulos pendentes de liquidação com paginação', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-pendente/v1/listar' => Http::response([
            'status' => 0,
            'pagina' => 1,
            'indMaisPagina' => 'S',
            'qtdeTitulos' => 2,
            'vtotTitulos' => 3000,
            'titulos' => [
                [
                    'nossoNumero' => 41970000001,
                    'valTitulo' => 1000,
                    'dataVencto' => '01.02.2026',
                    'pagador' => ['nome' => 'FULANO DE TAL', 'cnpjCpf' => 12345678, 'filial' => 0, 'controle' => 9],
                ],
                [
                    'nossoNumero' => 41970000002,
                    'valTitulo' => 2000,
                    'sacador' => ['nome' => 'AVALISTA LTDA'],
                ],
            ],
        ]),
    ]);

    $r = cobrancaConsultaMethods()->listarPendentes([
        'cpfCnpj' => ['cpfCnpj' => '11222333', 'filial' => '0001', 'controle' => '81'],
        'produto' => '09',
        'negociacao' => '38610041000',
        'paginaAnterior' => '0',
    ]);

    expect($r->indMaisPagina)->toBe('S')
        ->and($r->titulos)->toHaveCount(2)
        ->and($r->titulos[0]->pagador?->nome)->toBe('FULANO DE TAL')
        ->and($r->titulos[0]->pagador?->cnpjCpf)->toBe('12345678')
        ->and($r->titulos[1]->sacador?->nome)->toBe('AVALISTA LTDA');
});

it('lista títulos liquidados com totalizadores', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-lista/v1/listar' => Http::response([
            'status' => 0,
            'vtotTitulos' => 3000,
            'vtotPag' => 3000,
            'qtdeTitulos' => 1,
            'indMaisPagina' => 'N',
            'titulos' => [[
                'nossoNumero' => 41970000001,
                'digitoNossoNumero' => '5',
                'nomePagador' => 'FULANO DE TAL',
                'valorTitulo' => 3000,
                'valorPagamento' => 3000,
                'dataPagamento' => '05.02.2026',
                'descricaoFormaCredito' => 'CREDITO EM CONTA',
            ]],
        ]),
    ]);

    $r = cobrancaConsultaMethods()->listarLiquidados([
        'cpfCnpj' => ['cpfCnpj' => '11222333', 'filial' => '0001', 'controle' => '81'],
        'produto' => '09',
        'negociacao' => '38610041000',
        'dataPagamentoDe' => '01.02.2026',
        'dataPagamentoAte' => '28.02.2026',
    ]);

    expect($r->vtotPag)->toBe(3000)
        ->and($r->titulos)->toHaveCount(1)
        ->and($r->titulos[0]->descricaoFormaCredito)->toBe('CREDITO EM CONTA');
});

it('lista títulos baixados com cpfCnpj do sacado decomposto', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-baixado-consulta/v1/listar' => Http::response([
            'status' => 0,
            'qtdeTotalTitulos' => 1,
            'vtotTitulos' => 10.0,
            'indMaisPagina' => 'N',
            'pagina' => 1,
            'titulos' => [[
                'nossoNumero' => '41970000001',
                'nomeSacado' => 'FULANO DE TAL',
                'cpfCnpjSacado' => ['cpfCnpj' => 44256372, 'filial' => 1, 'controle' => 59],
                'dataBaixa' => '10.02.2026',
                'valorPago' => 1000,
                'descricaoStatusTitulo' => 'BAIXADO POR SOLICITACAO',
            ]],
        ]),
    ]);

    $r = cobrancaConsultaMethods()->listarBaixados([
        'versao' => 1,
        'cpfCnpj' => ['cpfCnpj' => 11222333, 'filial' => 1, 'controle' => 81],
        'produto' => 9,
        'negociacao' => 38610041000,
        'dataVencimentoDe' => 20260101,
        'dataVencimentoAte' => 20260228,
        'codigoBaixa' => 61,
    ]);

    expect($r->titulos)->toHaveCount(1)
        ->and($r->titulos[0]->cpfCnpjSacado?->cpfCnpj)->toBe('44256372')
        ->and($r->titulos[0]->cpfCnpjSacado?->controle)->toBe('59')
        ->and($r->vtotTitulos)->toBe(10.0);
});

it('consulta split payment com lista de rateio', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-consulta-split/v1/executar' => Http::response([
            'status' => 0,
            'transacao' => 'CBTTIAG3',
            'qlistaRteio' => 2,
            'indMaisPagina' => 'S',
            'restartSaida' => '0090399502739723005000000200',
            'listaRteio' => [
                ['cagBnefcRteio' => 4150, 'cctaBnefcRteio' => 561, 'vlrPercRteio' => '000000000001000', 'ibnefcRteioCredt' => 'BANCO DA AMAZONIA S/A.', 'floatRteioBnefc' => 5],
                ['cagBnefcRteio' => 4150, 'cctaBnefcRteio' => 703, 'vlrPercRteio' => '000000000001000', 'ibnefcRteioCredt' => 'BANCO GE CAPITAL S/A', 'floatRteioBnefc' => 5],
            ],
        ]),
    ]);

    $r = cobrancaSplitMethods()->consultar([
        'nvrsaoLyout' => 1,
        'cnpjCpf' => 249049448,
        'cflialCnpj' => 0,
        'cctrlCnpjCpf' => 10,
        'idProduto' => 9,
        'contaProduto' => 20001458,
        'nossoNumero' => 30200000002,
        'nseqTitulo' => 0,
    ]);

    expect($r->listaRteio)->toHaveCount(2)
        ->and($r->listaRteio[0]->ibnefcRteioCredt)->toBe('BANCO DA AMAZONIA S/A.')
        ->and($r->restartSaida)->toBe('0090399502739723005000000200');
});

it('faz manutenção do split payment devolvendo status por linha', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-manutencao-split/v1/manutencao-rateio-credito' => Http::response([
            'status' => 0,
            'qlistaRteio' => 1,
            'listaRteio' => [[
                'acaoRteio' => 'I',
                'cagBnefcRteio' => 4150,
                'cctaBnefcRteio' => 561,
                'vlrPercRteio' => '000000000001000',
                'statusAcaoRteio' => 'OK',
                'rmotvoStatusAcao' => 'RATEIO INCLUIDO',
            ]],
        ]),
    ]);

    $r = cobrancaSplitMethods()->manutencao([
        'nvrsaoLyout' => 1,
        'cnpjCpf' => '249049448',
        'cflialCnpj' => '0',
        'cctrlCnpjCpf' => '10',
        'idProduto' => '9',
        'contaProduto' => '20001458',
        'nossoNumero' => '30200000002',
        'nseqTitulo' => 0,
        'ccalcRteio' => 1,
        'ctpoVlrRteio' => 1,
        'canclRteio' => 'N',
        'listaRteio' => [['acaoRteio' => 'I', 'cagBnefcRteio' => 4150, 'cctaBnefcRteio' => 561]],
    ]);

    expect($r->listaRteio)->toHaveCount(1)
        ->and($r->listaRteio[0]->statusAcaoRteio)->toBe('OK')
        ->and($r->listaRteio[0]->rmotvoStatusAcao)->toBe('RATEIO INCLUIDO');
});

it('inclui webhook mandando tipoCadastro=I no payload', function () {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-webhook/v1/cadastrar' => Http::response([
            'utilizaWebhook' => 'S',
            'urlEnvio' => 'https://dominio.com.br/webhook',
            'datahoraAtualizacao' => '2026-02-01T10:00:00',
        ]),
    ]);

    $r = cobrancaWebhookMethods()->incluir(
        documento: ['cpfCnpj' => '112223330', 'filial' => '0001', 'controle' => '81'],
        urlEnvio: 'https://dominio.com.br/webhook',
        tipoAviso: 1,
    );

    expect($r->utilizaWebhook)->toBe('S')
        ->and($r->urlEnvio)->toBe('https://dominio.com.br/webhook')
        ->and($r->datahoraAtualizacao)->toBe('2026-02-01T10:00:00');

    Http::assertSent(fn ($request) => str_contains($request->url(), '/boleto/cobranca-webhook/v1/cadastrar')
        && $request->method() === 'POST'
        && $request['tipoCadastro'] === 'I'
        && $request['versaoLayout'] === '1'
        && $request['tipoAviso'] === 1
        && $request['documento']['controle'] === '81');
});

it('mapeia cada modo do webhook no tipoCadastro correspondente', function (string $metodo, string $esperado) {
    Http::fake(tokenFakesCobranca() + [
        '*/boleto/cobranca-webhook/v1/cadastrar' => Http::response([
            'utilizaWebhook' => 'N',
            'urlEnvio' => 'https://dominio.com.br/webhook',
        ]),
    ]);

    cobrancaWebhookMethods()->{$metodo}(
        documento: ['cpfCnpj' => '112223330', 'filial' => '0001', 'controle' => '81'],
        urlEnvio: 'https://dominio.com.br/webhook',
        tipoAviso: 1,
    );

    Http::assertSent(fn ($request) => str_contains($request->url(), '/boleto/cobranca-webhook/v1/cadastrar')
        && $request['tipoCadastro'] === $esperado);
})->with([
    ['incluir', 'I'],
    ['alterar', 'A'],
    ['consultar', 'C'],
    ['excluir', 'E'],
]);
