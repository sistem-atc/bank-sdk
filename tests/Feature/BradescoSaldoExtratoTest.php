<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato\ExtratoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato\SaldoResponse;
use SistemAtc\Banks\Bradesco\Endpoints\SaldoExtrato\SaldoExtratoMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedSaldoExtrato(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function saldoExtratoMethods(): SaldoExtratoMethods
{
    $i = authedSaldoExtrato();

    return new SaldoExtratoMethods(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API),
        $i,
    );
}

function fakeTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

/** Resposta de saldo conforme o example da spec. */
function saldoPayload(): array
{
    return [
        'codigoRetorno' => '0',
        'mensagem' => 'CTAS0673 - CONSULTA EFETUADA COM SUCESSO',
        'identificaoCliente' => '0',
        'razaoConta' => '00705',
        'numeroConta' => '0002142',
        'digitoConta' => '3',
        'nomeCliente' => 'JOAO CARLOS',
        'statusContaCorrente' => '0',
        'statusContaPoupanca' => '0',
        'identificadorTipoConta' => 'conta corrente',
        'statusCoberturaAutomatica' => '1',
        'contaPoupancaFacil' => '0000000',
        'dataUltimaAtualizacao' => '',
        'identificadorModalidadeConta' => '',
        'dataProximoPagamentoInss' => '00000000',
        'dataVencimentoCartaoInss' => '00000000',
        'statusCartaoInss' => '',
        'quantidadeLancamentos' => '2',
        'lstLancamentosSaldos' => [
            [
                'nomeProduto' => 'DISPONIVEL',
                'nomeProdutoResumido' => 'DISPONIVEL',
                'codigoProduto' => 999,
                'identificadorSaldo' => '0',
                'dataLancamentoDb2' => '',
                'valorLancamento' => '0,00',
                'sinalSaldo' => '+',
            ],
            [
                'nomeProduto' => '= TOTAL DE RECURSOS',
                'nomeProdutoResumido' => '= TOTAL DE RECURSOS',
                'codigoProduto' => 995,
                'identificadorSaldo' => '0',
                'dataLancamentoDb2' => 'A',
                'valorLancamento' => '1.580,12',
                'sinalSaldo' => '+',
            ],
        ],
    ];
}

/** Resposta de extrato conforme o example da spec (3 blocos). */
function extratoPayload(): array
{
    $lancamento = [
        'sinalSaldo' => '+',
        'valorLancamento' => '80',
        'segundaLinhalLancamento' => 'REM: EMPRESA PAGADOR       22/01',
        'sinalLancamento' => '+',
        'identificacaoSubCodigo' => 'N',
        'codigoLancamento' => '1674',
        'descritivoLancamentoAbreviado' => 'TRANSFE PIX',
        'numeroDocumento' => '0936269',
        'valorSaldoAposLancamento' => '80',
        'dataLancamento' => '01/01/2025',
        'descritivoLancamentoCompleto' => 'TRANSFERENCIA PIX',
    ];

    return [
        'extratoUltimosLancamentos' => [[
            'codigoRetorno' => '0',
            'mensagem' => 'CTAS0673 - CONSULTA EFETUADA COM SUCESSO',
            'numeroConta' => '0002142',
            'digitoConta' => '3',
            'nomeCliente' => 'JOAO CARLOS',
            'quantidadeLancamentos' => '3',
            'listaLancamentos' => [[
                'Saldo Anterior' => [$lancamento],
                'Ultimos Lancamentos' => [$lancamento],
                // A spec grafa este cabeçalho ora 'Lancamentos Dia', ora 'Lancamentos dia'.
                'Lancamentos dia' => [['sinalLacamento' => '-', 'valorLancamento' => '1.234,56', 'dataLancamento' => '02/01/2025', 'numeroDocumento' => '000111', 'descritivoLancamentoCompleto' => 'DEBITO TARIFA']],
            ]],
        ]],
        'extratoLancamentosFuturos' => [[
            'codigoRetorno' => '0',
            'quantidadeLancamentos' => '1',
            'listaLancamentos' => [[
                'Lancamentos Futuros' => [$lancamento],
            ]],
        ]],
        'extratoPorPeriodo' => [[
            'codigoRetorno' => '0',
            'identificaoCliente' => '1',
            'razaoConta' => '00705',
            'numeroConta' => '0075557',
            'digitoConta' => '5',
            'nomeCliente' => 'CONTA AMBIENTE TU',
            'quantidadeLancamentos' => '1',
            'lstLancamentoMensal' => [[
                'dataLancamento' => '30/09/2024',
                'numeroDocumento' => '0000000',
                'valorLancamento' => 0,
                'sinalLancamento' => '+',
                'segundaLinhalLancamento' => '',
                'valorSaldoAposLancamento' => 8000,
                'sinalSaldo' => '+',
                'identificacaoSubCodigo' => '',
                'tipoLancamento' => '01',
                'codigoLancamento' => '00000',
                'descritivoLancamentoAbreviado' => 'Saldo Anterior',
                'descritivoLancamentoCompleto' => 'Saldo Anterior',
                'dataDebitoCpmf' => '0000000',
                'valorCpmf' => '000000000000000',
            ]],
        ]],
    ];
}

it('consulta saldo PJ (GET /v1/fornecimento-saldos-contas/saldos) e expoe a composicao por produto', function () {
    Http::fake(fakeTokens() + [
        '*/v1/fornecimento-saldos-contas/saldos*' => Http::response(saldoPayload()),
    ]);

    $res = saldoExtratoMethods()->saldos(3750, 75557);

    expect($res)->toBeInstanceOf(SaldoResponse::class)
        ->and($res->sucesso())->toBeTrue()
        ->and($res->nomeCliente)->toBe('JOAO CARLOS')
        ->and($res->numeroConta)->toBe('0002142')
        ->and($res->lstLancamentosSaldos)->toHaveCount(2)
        ->and($res->disponivel())->toBe(0.0)
        ->and($res->totalDeRecursos())->toBe(1580.12)
        ->and($res->produto(995)?->nomeProduto)->toBe('= TOTAL DE RECURSOS');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v1/fornecimento-saldos-contas/saldos')
        && str_contains($r->url(), 'agencia=3750')
        && str_contains($r->url(), 'conta=75557')
        && $r->method() === 'GET');
});

it('envia tipoOperacao no saldo so quando informado', function () {
    Http::fake(fakeTokens() + [
        '*/v1/fornecimento-saldos-contas/saldos*' => Http::response(saldoPayload()),
    ]);

    saldoExtratoMethods()->saldos(3750, 75557, tipoOperacao: '1');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/saldos')
        && str_contains($r->url(), 'tipoOperacao=1'));
});

it('consulta extrato PJ (GET /v1/fornecimento-extratos-contas/extratos) com a janela em DDMMAAAA', function () {
    Http::fake(fakeTokens() + [
        '*/v1/fornecimento-extratos-contas/extratos*' => Http::response(extratoPayload()),
    ]);

    $res = saldoExtratoMethods()->extratos(3750, 75557, 'cc', '2024-11-06', '2024-11-20');

    expect($res)->toBeInstanceOf(ExtratoResponse::class)
        ->and($res->extratoUltimosLancamentos)->toHaveCount(1)
        ->and($res->extratoLancamentosFuturos)->toHaveCount(1)
        ->and($res->extratoPorPeriodo)->toHaveCount(1)
        ->and($res->extratoPorPeriodo[0]->nomeCliente)->toBe('CONTA AMBIENTE TU');

    // Datas normalizadas pro formato do banco (DDMMAAAA) e tipo obrigatorio.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/v1/fornecimento-extratos-contas/extratos')
        && str_contains($r->url(), 'dataInicio=06112024')
        && str_contains($r->url(), 'dataFim=20112024')
        && str_contains($r->url(), 'tipo=cc'));
});

it('achata os tres blocos do extrato em lancamentos de conciliacao', function () {
    Http::fake(fakeTokens() + [
        '*/v1/fornecimento-extratos-contas/extratos*' => Http::response(extratoPayload()),
    ]);

    $lancamentos = saldoExtratoMethods()->lancamentos(3750, 75557, 'cc', '06112024', '20112024');

    // 3 do bloco de ultimos (saldo anterior + ultimos + dia) + 1 futuro + 1 do periodo.
    expect($lancamentos)->toHaveCount(5)
        ->and($lancamentos[0]->historico())->toBe('TRANSFERENCIA PIX')
        ->and($lancamentos[0]->ehCredito())->toBeTrue()
        ->and($lancamentos[0]->valor())->toBe(80.0)
        ->and($lancamentos[0]->numeroDocumento)->toBe('0936269')
        ->and($lancamentos[0]->data()?->format('Y-m-d'))->toBe('2025-01-01');
});

it('normaliza o cabecalho "Lancamentos dia" e o typo sinalLacamento (debito vira valor negativo)', function () {
    Http::fake(fakeTokens() + [
        '*/v1/fornecimento-extratos-contas/extratos*' => Http::response(extratoPayload()),
    ]);

    $bloco = saldoExtratoMethods()->extratos(3750, 75557, 'cc', '06112024', '20112024')->extratoUltimosLancamentos[0];
    $grupo = $bloco->listaLancamentos[0];

    expect($grupo->saldoAnterior)->toHaveCount(1)
        ->and($grupo->ultimosLancamentos)->toHaveCount(1)
        ->and($grupo->lancamentosDia)->toHaveCount(1);

    $debito = $grupo->lancamentosDia[0];

    expect($debito->sinalLancamento)->toBe('-')
        ->and($debito->ehDebito())->toBeTrue()
        ->and($debito->valor())->toBe(-1234.56)
        ->and($debito->historico())->toBe('DEBITO TARIFA');
});

it('fatia janelas longas em varias chamadas (o contrato nao tem paginacao)', function () {
    Http::fake(fakeTokens() + [
        '*/v1/fornecimento-extratos-contas/extratos*' => Http::response(extratoPayload()),
    ]);

    $lancamentos = saldoExtratoMethods()->extratoFatiado(3750, 75557, '2025-01-01', '2025-01-20', 'cc', diasPorJanela: 10);

    // 2 janelas (01-10 e 11-20) x 5 lancamentos.
    expect($lancamentos)->toHaveCount(10);

    Http::assertSent(fn ($r) => str_contains($r->url(), 'dataInicio=01012025') && str_contains($r->url(), 'dataFim=10012025'));
    Http::assertSent(fn ($r) => str_contains($r->url(), 'dataInicio=11012025') && str_contains($r->url(), 'dataFim=20012025'));
});

it('mantem o roundtrip fromArray/toArray dos blocos do extrato', function () {
    $res = ExtratoResponse::fromArray(extratoPayload());
    $array = $res->toArray();

    expect($array)->toHaveKeys(['extrato_ultimos_lancamentos', 'extrato_lancamentos_futuros', 'extrato_por_periodo'])
        // As chaves-cabecalho do banco sao preservadas na volta.
        ->and($array['extrato_ultimos_lancamentos'][0]['lista_lancamentos'][0])
        ->toHaveKeys(['Saldo Anterior', 'Ultimos Lancamentos', 'Lancamentos Dia']);
});
