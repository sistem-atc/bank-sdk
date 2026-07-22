<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\Agendamento;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\PagamentoDevolvido;
use SistemAtc\Banks\Bradesco\Endpoints\PagamentoBoletos\PagamentoBoletosMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/** Integração Bradesco autenticável (produção, família open_api). */
function authedPagamentoBoletos(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function pagamentoBoletosMethods(): PagamentoBoletosMethods
{
    $i = authedPagamentoBoletos();

    return new PagamentoBoletosMethods(
        HttpClientFactory::make($i, BradescoHosts::FAMILY_OPEN_API),
        $i,
    );
}

/** Fakes dos DOIS autorizadores + a rota do produto. */
function fakePagamentoBoletos(string $pattern, array $body): void
{
    Http::fake([
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        $pattern => Http::response($body),
    ]);
}

it('consulta parâmetros e limites de pagamento (PASSO 0)', function () {
    fakePagamentoBoletos('*/cobranca-parametros-pgto/executar*', [
        'status' => 200,
        'transacao' => 'CBCAIAA1',
        'mensagem' => 'Operação realizada com sucesso',
        'horaEncerBrad' => 200000,
        'limDispTotalBrad' => 15000.55,
        'pagtCartaoCredt' => 'S',
    ]);

    $res = pagamentoBoletosMethods()->consultarParametros([
        'bancoCliente' => '237',
        'agenciaCliente' => '12345',
        'digitoAgencia' => '1',
        'contaCliente' => '1234567',
        'digitoConta' => '9',
    ]);

    expect($res->status)->toBe(200)
        ->and($res->transacao)->toBe('CBCAIAA1')
        ->and($res->limDispTotalBrad)->toBe(15000.55)
        ->and($res->pagtCartaoCredt)->toBe('S');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-parametros-pgto/executar')
        && $r->method() === 'POST'
        && $r['agenciaCliente'] === '12345');
});

it('valida o título de pagamento pelo código de barras (PASSO 1)', function () {
    fakePagamentoBoletos('*/cobranca-valida-titulo-pagamento/validaTituloPagamento*', [
        'status' => 200,
        'transacao' => 'CBCAIAA2',
        'mensagem' => 'Operação realizada com sucesso',
        'dataVencimento' => 20261231,
        'codBancoConsulta' => 237,
        'nmBanco' => 'BANCO BRADESCO S.A.',
        'nmCedente' => 'FORNECEDOR TESTE LTDA',
        'valorTitulo' => 348.97,
        'valorCobrado' => 348.97,
        'valorMin' => 0.01,
        'valorMax' => 9999.99,
        'permitePgtoParcial' => 'N',
        'consultaCip' => 'S',
        'numeroCtrlCip' => '000000000012345',
        // Documento com zero à esquerda: precisa sobreviver como string.
        'cnpjCpfCedente' => '09538989000166',
    ]);

    $res = pagamentoBoletosMethods()->validarTitulo([
        'tipoEntrada' => 1,
        'dadosEntrada' => '000000000023795106800000011112856090029995343202226520',
        'bancoTitulo' => 237,
        'agenciaDeb' => 2856,
        'contaDeb' => 50035,
    ]);

    expect($res->nmCedente)->toBe('FORNECEDOR TESTE LTDA')
        ->and($res->valorCobrado)->toBe(348.97)
        ->and($res->dataVencimento)->toBe(20261231)
        ->and($res->consultaCip)->toBe('S')
        ->and($res->cnpjCpfCedente)->toBe('09538989000166');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-valida-titulo-pagamento/validaTituloPagamento')
        && $r->method() === 'POST'
        && $r['tipoEntrada'] === 1);
});

it('pré-efetiva o pagamento e devolve o valor a debitar + protocolo (PASSO 2)', function () {
    fakePagamentoBoletos('*/cobranca-pre-efetivacao/pre-efetivacao-pagamento*', [
        'status' => 200,
        'transacao' => 'CBCAIAA3',
        'mensagem' => 'Operação realizada com sucesso',
        'nomeCedente' => 'FORNECEDOR TESTE LTDA',
        'nroProtocolo' => 788,
        'valorTitulo' => 348.97,
        'valorCobrado' => 348.97,
        'dataVctoTitlo' => 20261231,
        'linhaDigitavel1' => '23792.85600 95127.000024 51022.265204 1',
        'linhaDigitavel2' => 13110000000100,
    ]);

    $res = pagamentoBoletosMethods()->preEfetivar([
        'codigoAgencia' => 2856,
        'formaCaptura' => 1,
        'codigoBarras' => '23797100500000011213861090097002841600410000',
        'dataVencimento' => 20261231,
        'valorTitulo' => 348.97,
        'dataMovimento' => 20260722,
        'dataPagamento' => 20260722,
        'horaTransacao' => 143500,
        'formaPagamento' => 2,
        'bancoDebito' => 237,
        'agenciaDebito' => 2856,
        'contaDebito' => 50035,
        'digitoConta' => '0',
        'cnpjCpfPtdor' => 123456789,
        'filialCnpjPtdor' => 0,
        'ctrlCnpjPtdor' => 82,
    ]);

    expect($res->nroProtocolo)->toBe(788)
        ->and($res->valorCobrado)->toBe(348.97)
        // integer na spec, mas guardamos string p/ não perder zero à esquerda.
        ->and($res->linhaDigitavel2)->toBe('13110000000100');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-pre-efetivacao/pre-efetivacao-pagamento'));
});

it('efetiva o pagamento — passo que DEBITA a conta (PASSO 3)', function () {
    fakePagamentoBoletos('*/cobranca-efetivacao/solicitacao/executar*', [
        'status' => 200,
        'transacao' => 'CBCAIAAY',
        'mensagem' => 'Operação realizada com sucesso.',
        'causa' => 'CBCA0001 - PAGAMENTO EFETUADO',
        'nomeCedente' => 'FORNECEDOR TESTE LTDA',
        'nroProtocolo' => 788,
        'valorTitulo' => 348.97,
        'valorCobrado' => 348.97,
        'dataQuitacao' => 20260722,
        'linhaDigitavel1' => '23792.85600 95127.000024 51022.265204 1',
        'cnpjCpfSacado' => '09538989000166',
        'codBancoReceb' => 237,
    ]);

    $res = pagamentoBoletosMethods()->efetivar([
        'codigoAgencia' => 2856,
        'formaCaptura' => 2,
        'codigoBarras' => '23791131100000001002856095127000025102226520',
        'dataVencimento' => 20261231,
        'valorTitulo' => 34897,
        'dataMovimento' => 20260722,
        'dataPagamento' => 20260722,
        'horaTransacao' => 93104,
        'formaPagamento' => 2,
        'indicadorFuncao' => '1',
        'bancoDebito' => 237,
        'agenciaDebito' => 2856,
        'contaDebito' => 50035,
        'digitoConta' => '0',
        'transactionId' => 22,
        'cnpjCpfPtdor' => 123456789,
        'filialCnpjPtdor' => 0,
        'ctrlCnpjPtdor' => 82,
    ]);

    expect($res->causa)->toBe('CBCA0001 - PAGAMENTO EFETUADO')
        ->and($res->nroProtocolo)->toBe(788)
        ->and($res->valorCobrado)->toBe(348.97)
        ->and($res->dataQuitacao)->toBe(20260722)
        ->and($res->cnpjCpfSacado)->toBe('09538989000166');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-efetivacao/solicitacao/executar')
        && $r->method() === 'POST'
        && $r['transactionId'] === 22);
});

it('lista agendamentos e pagamentos com paginação por restart', function () {
    fakePagamentoBoletos('*/cobranca-agendamentos-pgto/listar*', [
        'status' => 200,
        'transacao' => 'CBCAIAA6',
        'mensagem' => 'Operação realizada com sucesso',
        'indMaisPagina' => 'S',
        'restartSaida' => 'ABC123',
        'quantidadePagtosLista' => 2,
        'totalRegistrosLista' => 2,
        'valorTotalPagtosLista' => 1000.0,
        'agendamentos' => [
            [
                'bancoEmissor' => 655,
                'dataPagamento' => '20220128',
                'dataVencimento' => '20220129',
                'idInformadoAPI' => 0,
                'motivoNaoEfetivacao' => 3,
                'descricaoMotivoNaoEfetivacao' => 'SALDO INSUFICIENTE',
                'protocoloPagamento' => 957,
                'valorCalculado' => 500,
                'valorInformado' => 500,
            ],
            [
                'bancoEmissor' => 237,
                'dataPagamento' => '20220128',
                'dataVencimento' => '20220130',
                'protocoloPagamento' => 958,
                'valorCalculado' => 500,
                'valorInformado' => 500,
            ],
        ],
    ]);

    $res = pagamentoBoletosMethods()->listarAgendamentos([
        'versaoLayout' => 1,
        'bancoDaContaDebito' => 237,
        'agenciaDaContaDebito' => 3995,
        'contaCorrenteDebito' => 75557,
        'dataInicial' => '20250125',
        'dataFinal' => '20250225',
        'situacaoPagamento' => 1,
        'restartEntrada' => '',
    ]);

    expect($res->agendamentos)->toHaveCount(2)
        ->and($res->agendamentos[0])->toBeInstanceOf(Agendamento::class)
        ->and($res->agendamentos[0]->protocoloPagamento)->toBe(957)
        ->and($res->agendamentos[0]->descricaoMotivoNaoEfetivacao)->toBe('SALDO INSUFICIENTE')
        ->and($res->indMaisPagina)->toBe('S')
        ->and($res->restartSaida)->toBe('ABC123');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-agendamentos-pgto/listar'));
});

it('altera um agendamento de pagamento', function () {
    fakePagamentoBoletos('*/cobranca-alterar-agendamento/alteracao/executar*', [
        'status' => 200,
        'transacao' => 'CBCAIAA4',
        'mensagem' => 'Operação realizada com sucesso',
        'dataPagamento' => 20250521,
        'valorPagamento' => 5000.0,
        'descricaoBoleto' => 'BOLETO TESTE',
    ]);

    $res = pagamentoBoletosMethods()->alterarAgendamento([
        'versao' => 2,
        'bancoDebito' => 237,
        'agenciaDebito' => 2856,
        'digitoAgencia' => 0,
        'contaDebito' => 50035,
        'digitoConta' => '0',
        'numeroProtocolo' => 821,
        'dataPagamento' => 20250521,
        'valorPagamento' => 5000.0,
    ]);

    expect($res->status)->toBe(200)
        ->and($res->dataPagamento)->toBe(20250521)
        ->and($res->valorPagamento)->toBe(5000.0);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-alterar-agendamento/alteracao/executar')
        && $r['numeroProtocolo'] === 821);
});

it('exclui um agendamento de pagamento', function () {
    fakePagamentoBoletos('*/cobranca-excluir-agendamento/exclusao/executar*', [
        'status' => 200,
        'transacao' => 'CBCAIAA5',
        'mensagem' => 'Operação realizada com sucesso',
        'causa' => '',
    ]);

    $res = pagamentoBoletosMethods()->excluirAgendamento([
        'versao' => 2,
        'bancoDebito' => 237,
        'agenciaDebito' => 2856,
        'digitoAgencia' => 0,
        'contaDebito' => 50035,
        'digitoConta' => '0',
        'numeroProtocolo' => 821,
    ]);

    expect($res->status)->toBe(200)
        ->and($res->transacao)->toBe('CBCAIAA5');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-excluir-agendamento/exclusao/executar'));
});

it('consulta um pagamento específico por protocolo', function () {
    fakePagamentoBoletos('*/cobranca-pagamento-consulta/consulta-pagamento-especifico*', [
        'status' => 200,
        'transacao' => 'CBCAIAA7',
        'mensagem' => 'Operação realizada com sucesso',
        'situacaoPagamento' => 1,
        'numeroProtocolo' => 1,
        'transactionId' => 857496259,
        'valorPagamento' => 348.97,
        'dataVencimento' => 20261231,
        'digitavel1' => '23792.85600 95127.000024 51022.265204 1',
        'nomeBancoDestino' => 'BANCO BRADESCO S.A.',
        'ccgcCpfSacdo' => '09538989000166',
    ]);

    $res = pagamentoBoletosMethods()->consultarPagamento([
        'versaoLayout' => '1',
        'numeroBanco' => '237',
        'numeroAgencia' => '448',
        'numeroConta' => '1234567',
        'numeroProtocolo' => '1',
        'dataPagamento' => '20250313',
    ]);

    expect($res->numeroProtocolo)->toBe(1)
        ->and($res->situacaoPagamento)->toBe(1)
        ->and($res->valorPagamento)->toBe(348.97)
        ->and($res->ccgcCpfSacdo)->toBe('09538989000166');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-pagamento-consulta/consulta-pagamento-especifico'));
});

it('lista os pagamentos devolvidos do período', function () {
    fakePagamentoBoletos('*/cobranca-lista-pagamento-devolvido/listar*', [
        'status' => 200,
        'transacao' => 'CBCAIAA8',
        'mensagem' => 'Operação realizada com sucesso',
        'indMaisPagina' => 'N',
        'quantidadePagtosDevolvidos' => 1,
        'valorTotalPagtosDevolvidos' => 4006.06,
        'totalRegistrosLista' => 1,
        'listaPagtosDevolvidos' => [
            [
                'dataDevolucaoPagto' => '20220615',
                'dataPagamento' => '20220614',
                'protocoloPagamento' => 119,
                'idInformadoAPI' => 1015,
                'valorInformado' => 4006.06,
                'linhaDigitavelTituloCobranca' => '23792.85600 90029.994648 13022.265204 9 90020000000000',
                'motivoNaoEfetivacao' => 10,
                'descricaoMotivoNaoEfetivacao' => 'VALOR COBRADO INVALIDO',
            ],
        ],
    ]);

    $res = pagamentoBoletosMethods()->listarDevolvidos([
        'versaoLayout' => 1,
        'bancoDebito' => 237,
        'agenciaDebito' => 1234,
        'contaDebito' => 12345,
        'dataDevolucaoInicial' => '20190704',
        'dataDevolucaoFinal' => '20220615',
        'restartEntrada' => '',
    ]);

    expect($res->listaPagtosDevolvidos)->toHaveCount(1)
        ->and($res->listaPagtosDevolvidos[0])->toBeInstanceOf(PagamentoDevolvido::class)
        ->and($res->listaPagtosDevolvidos[0]->motivoNaoEfetivacao)->toBe(10)
        ->and($res->listaPagtosDevolvidos[0]->descricaoMotivoNaoEfetivacao)->toBe('VALOR COBRADO INVALIDO')
        ->and($res->valorTotalPagtosDevolvidos)->toBe(4006.06);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/pagamento-cobranca/v1/cobranca-lista-pagamento-devolvido/listar'));
});
