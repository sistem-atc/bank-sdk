<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao\ConsultaPagamentosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao\PagamentoEfetivacaoResponse;
use SistemAtc\Banks\Bradesco\Endpoints\Arrecadacao\ArrecadacaoMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function authedArrecadacao(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function arrecadacaoMethods(): ArrecadacaoMethods
{
    $i = authedArrecadacao();

    return new ArrecadacaoMethods(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API),
        $i,
    );
}

function bradescoArrecadacaoTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
    ];
}

it('pre-confirma o pagamento com tipoRegistro=0 sem debitar', function () {
    Http::fake(bradescoArrecadacaoTokens() + [
        '*/pagamento/arrecadacao-via-codbarras/v1/pagamentoContaConsumo*' => Http::response([
            'retorno' => 0,
            'banco' => 237,
            'agencia' => 3995,
            'conta' => 75557,
            'tipoRegistro' => '0',
            'nomeFantasia' => 'SABESP/SP',
            'identificacaoPagamento' => 'AGUA',
            'obrigaDigitarValorDebito' => 'N',
            'valorTributo' => 93.07,
            'valorPago' => 93.07,
            'dataVencimento' => '2024-03-27',
            'codigoBarras' => '826800000009307009714932046964401510157210121',
        ]),
    ]);

    $res = arrecadacaoMethods()->preConfirmar([
        'agencia' => 3995,
        'digitoAgencia' => 0,
        'conta' => 75557,
        'digitoConta' => '7',
        'codigoBarras' => '82680000000930700971493204696440151015721012',
        'dataDebito' => '2024-03-27',
        'valorPrincipal' => 93.07,
        'tipoConta' => 1,
        'descricaoCliente' => 'Conta de agua',
        'idTransacao' => '204uu',
    ]);

    expect($res)->toBeInstanceOf(PagamentoEfetivacaoResponse::class)
        ->and($res->retorno)->toBe(0)
        ->and($res->nomeFantasia)->toBe('SABESP/SP')
        ->and($res->obrigaDigitarValorDebito)->toBe('N')
        ->and($res->valorTributo)->toBe(93.07);

    Http::assertSent(function ($r) {
        if (! str_contains($r->url(), '/pagamento/arrecadacao-via-codbarras/v1/pagamentoContaConsumo')) {
            return false;
        }

        return $r->method() === 'POST'
            && $r['tipoRegistro'] === 0
            && $r['codigoBarras'] === '82680000000930700971493204696440151015721012'
            && $r['valorPrincipal'] === 93.07;
    });
});

it('efetiva o pagamento com tipoRegistro=1 e devolve a autenticacao bancaria', function () {
    Http::fake(bradescoArrecadacaoTokens() + [
        '*/pagamento/arrecadacao-via-codbarras/v1/pagamentoContaConsumo*' => Http::response([
            'retorno' => 0,
            'sqlCode' => 0,
            'banco' => 237,
            'agencia' => 3963,
            'conta' => 404,
            'tipoConta' => 1,
            'contaDebito' => 404,
            'nomeCliente' => 'SOLDIERS NUTRITION LTDA',
            'tipoRegistro' => '1',
            'autenticacaoBancaria' => 45154760,
            'dataDebito' => '2021-03-11',
            'valorPago' => 52.62,
            'valorMulta' => 0,
            'tipoComprovante' => 3,
            'nomeEmpresaConveniada' => 'SABESP',
        ]),
    ]);

    $res = arrecadacaoMethods()->efetivar([
        'agencia' => 3963,
        'conta' => 404,
        'codigoBarras' => '82680000000930700971493204696440151015721012',
        'dataDebito' => '2021-03-11',
        'valorPrincipal' => 52.62,
        'tipoConta' => 1,
        'idTransacao' => '204uu',
        'tipoRegistro' => 0, // deve ser sobrescrito por 1
    ]);

    expect($res->autenticacaoBancaria)->toBe(45154760)
        ->and($res->valorPago)->toBe(52.62)
        ->and($res->tipoRegistro)->toBe('1')
        ->and($res->nomeEmpresaConveniada)->toBe('SABESP');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'pagamentoContaConsumo')
        && $r['tipoRegistro'] === 1);
});

it('consulta pagamentos por agencia/conta/tipoConta hidratando regSaida', function () {
    Http::fake(bradescoArrecadacaoTokens() + [
        '*/pagamento/arrecadacao-via-codbarras/v1/3750/75557/1*' => Http::response([
            [
                'banco' => 237,
                'agencia' => 3750,
                'conta' => 75557,
                'contaLinkada' => 75557,
                'retorno' => '000',
                'numeroSequencia' => 1,
                'restart' => 0,
                'contr' => 0,
                'regSaida' => [
                    [
                        'codigoBarras' => '82680000000930700971493204696440151015721012',
                        'codigoBarrasComDigito' => '826800000009307009714932046964401510157210121',
                        'identificacaoPagamento' => 'AGUA',
                        'nomeEmpresaConveniada' => 'SABESP/SP',
                        'valorDebito' => 93.07,
                        'dataPagamento' => '2024-03-26',
                        'autenticacaoBancaria' => 123456789,
                        'tipoComprovante' => '03',
                    ],
                    [
                        'codigoBarras' => '85800000012345678901234567890123456789012345',
                        'identificacaoPagamento' => 'DARF',
                        'valorDebito' => 1200.5,
                    ],
                ],
            ],
        ]),
    ]);

    $blocos = arrecadacaoMethods()->consultar(
        agencia: 3750,
        conta: 75557,
        tipoConta: 1,
        tipoConsulta: 3,
        segmentoConsulta: 99,
        dataInicial: '2024-03-26',
        dataFinal: '2024-03-26',
        idTransacao: '502R',
    );

    expect($blocos)->toHaveCount(1)
        ->and($blocos[0])->toBeInstanceOf(ConsultaPagamentosResponse::class)
        ->and($blocos[0]->retorno)->toBe('000')
        ->and($blocos[0]->restart)->toBe(0)
        ->and($blocos[0]->regSaida)->toHaveCount(2)
        ->and($blocos[0]->regSaida[0]->nomeEmpresaConveniada)->toBe('SABESP/SP')
        ->and($blocos[0]->regSaida[0]->valorDebito)->toBe(93.07)
        ->and($blocos[0]->regSaida[1]->identificacaoPagamento)->toBe('DARF');

    Http::assertSent(function ($r) {
        if (! str_contains($r->url(), '/pagamento/arrecadacao-via-codbarras/v1/3750/75557/1')) {
            return false;
        }

        return $r->method() === 'GET'
            && str_contains($r->url(), 'tipoConsulta=3')
            && str_contains($r->url(), 'segmentoConsulta=99')
            && str_contains($r->url(), 'dataInicial=2024-03-26')
            && str_contains($r->url(), 'dataFinal=2024-03-26')
            && str_contains($r->url(), 'idTransacao=502R');
    });
});

it('usa a familia OPEN_API (host openapi), nao o autorizador Pix', function () {
    Http::fake(bradescoArrecadacaoTokens() + [
        '*/pagamento/arrecadacao-via-codbarras/v1/pagamentoContaConsumo*' => Http::response(['retorno' => 0]),
    ]);

    arrecadacaoMethods()->preConfirmar([
        'agencia' => 3995,
        'conta' => 75557,
        'codigoBarras' => '82680000000930700971493204696440151015721012',
        'dataDebito' => '2024-03-27',
        'valorPrincipal' => 93.07,
    ]);

    Http::assertSent(fn ($r) => str_contains($r->url(), 'pagamentoContaConsumo')
        && ! str_contains($r->url(), 'qrpix'));
});
