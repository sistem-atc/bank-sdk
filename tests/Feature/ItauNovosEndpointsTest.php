<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Banks\Itau\Itau;
use SistemAtc\Banks\Banks\Itau\DTO\Response\BoletoNegociado\AtivosFinanceirosList;
use SistemAtc\Banks\Banks\Itau\DTO\Response\BoletoNegociado\BoletoNegociado;
use SistemAtc\Banks\Banks\Itau\DTO\Response\BoletoNegociado\BoletosNegociadosList;
use SistemAtc\Banks\Banks\Itau\DTO\Response\Conciliacao\LancamentoPix;
use SistemAtc\Banks\Banks\Itau\DTO\Response\Conciliacao\LancamentosPixList;
use SistemAtc\Banks\Banks\Itau\Endpoints\BoletoNegociado\BoletoNegociadoMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\Conciliacao\ConciliacaoMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\RecebimentosPix\QrCodeMethods as PixQrCodeMethods;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/** Integração autenticada em PRODUÇÃO (pra validar os hosts reais). */
function itauNovoProd(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    $i = new FakeBankIntegration(sandbox: false);
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

// ---- Bug fixes Pix Automático --------------------------------------------

it('Pix Automático: /rec agora carrega o prefixo /pixautomatico/v1', function () {
    Http::fake(['*' => Http::response(['idRec' => 'RN1'])]);

    Itau::make()->pixAutomatico(itauNovoProd())->recorrencias()->consultar('RN1');

    Http::assertSent(fn ($r) => str_starts_with(
        $r->url(),
        'https://pixautomatico-recebimentos.api.itau.com/pixautomatico/v1/rec/RN1',
    ));
});

it('Pix Automático: dados-pagador usa /recorrencia (não /rec)', function () {
    Http::fake(['*' => Http::response(['pagador' => []])]);

    Itau::make()->pixAutomatico(itauNovoProd())->recorrencias()->consultarDadosPagador('RN1');

    Http::assertSent(fn ($r) => $r->method() === 'GET' && str_contains(
        $r->url(),
        '/pixautomatico/v1/recorrencia/RN1/dados-pagador',
    ));
});

it('Pix Automático: desvincularLocation usa segmento literal idRec', function () {
    Http::fake(['*' => Http::response([])]);

    Itau::make()->pixAutomatico(itauNovoProd())->recorrencias()->desvincularLocation('108');

    Http::assertSent(fn ($r) => $r->method() === 'DELETE'
        && str_ends_with($r->url(), '/pixautomatico/v1/locrec/108/idRec'));
});

it('Pix Automático: cancelar cobrança QR faz PATCH /cobrancas/{id}', function () {
    Http::fake(['*' => Http::response(['status' => 'CANCELADA'])]);

    Itau::make()->pixAutomatico(itauNovoProd())->qrCode()->cancelar('COB1', ['status' => 'CANCELADA']);

    Http::assertSent(fn ($r) => $r->method() === 'PATCH'
        && str_ends_with($r->url(), '/qrcode-pix-automatico/v1/cobrancas/COB1'));
});

// ---- Resolvers públicos de QR (regulatório Pix) --------------------------

it('Recebimentos Pix: resolver de QR imediato bate em /regulatorio-pix/v2/qr/{token}', function () {
    Http::fake(['*' => Http::response(['revisao' => 0])]);

    $out = Itau::make()->recebimentosPix(itauNovoProd())->qrCode()->imediata('TOKEN123');

    expect($out)->toBeArray();
    Http::assertSent(fn ($r) => str_starts_with(
        $r->url(),
        'https://pix-pj.api.itau.com/regulatorio-pix/v2/qr/TOKEN123',
    ));
});

it('Recebimentos Pix: resolver de QR com vencimento bate em /qr/cobv/{token}', function () {
    Http::fake(['*' => Http::response([])]);

    $receb = Itau::make()->recebimentosPix(itauNovoProd());
    expect($receb->qrCode())->toBeInstanceOf(PixQrCodeMethods::class);

    $receb->qrCode()->comVencimento('TOKEN123');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/regulatorio-pix/v2/qr/cobv/TOKEN123'));
});

// ---- GET /boletos do cash_management -------------------------------------

it('Boletos: listar bate em GET /cash_management/v2/boletos', function () {
    Http::fake(['*' => Http::response(['data' => []])]);

    Itau::make()->boletos(itauNovoProd())->emissao()->listar(['page' => 1]);

    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_starts_with($r->url(), 'https://api.itau.com.br/cash_management/v2/boletos'));
});

// ---- Conciliação Pix ------------------------------------------------------

it('Conciliação: lista lançamentos Pix desembrulhando data[] pra itens[]', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                ['id_lancamento' => 'L1', 'tipo_lancamento' => 'pagamento', 'tipo_operacao' => 'credito', 'detalhe_pagamento' => ['valor' => 99]],
                ['id_lancamento' => 'L2', 'tipo_lancamento' => 'devolucao'],
            ],
        ]),
    ]);

    $list = Itau::make()->conciliacao(itauNovoProd())->lancamentos(['data_lancamento' => '2026-08-01,2026-08-24']);

    expect($list)->toBeInstanceOf(LancamentosPixList::class)
        ->and($list->itens)->toHaveCount(2)
        ->and($list->itens[0])->toBeInstanceOf(LancamentoPix::class)
        ->and($list->itens[0]->idLancamento)->toBe('L1')
        ->and($list->itens[0]->tipoOperacao)->toBe('credito')
        ->and($list->itens[0]->detalhePagamento['valor'])->toBe(99);

    Http::assertSent(fn ($r) => str_starts_with(
        $r->url(),
        'https://pix-pj.api.itau.com/conciliacao/v1/lancamentos-pix',
    ));
});

it('Conciliação: consulta um lançamento por id', function () {
    Http::fake(['*' => Http::response(['data' => ['id_lancamento' => 'L9', 'e2eid' => 'E123']])]);

    $lanc = Itau::make()->conciliacao(itauNovoProd())->lancamento('L9');

    expect($lanc)->toBeInstanceOf(LancamentoPix::class)
        ->and($lanc->idLancamento)->toBe('L9')
        ->and($lanc->e2eid)->toBe('E123');

    Http::assertSent(fn ($r) => str_ends_with($r->url(), '/conciliacao/v1/lancamentos-pix/L9'));
});

// ---- Boletos Negociados / Ativos Financeiros ------------------------------

it('Boletos Negociados: lista boletos hidratando itens[] e pagador cru', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                ['idBoleto' => 'B1', 'valor' => '200.00', 'situacao' => 'aberto', 'pagador' => ['nomePagador' => 'Maria']],
            ],
            'page' => ['quantidadeTotalItens' => 1],
        ]),
    ]);

    $list = Itau::make()->boletoNegociado(itauNovoProd())->listarBoletos(['cpfPagador' => '33378996846']);

    expect($list)->toBeInstanceOf(BoletosNegociadosList::class)
        ->and($list->itens[0])->toBeInstanceOf(BoletoNegociado::class)
        ->and($list->itens[0]->idBoleto)->toBe('B1')
        ->and($list->itens[0]->valor)->toBe('200.00')
        ->and($list->itens[0]->pagador['nomePagador'])->toBe('Maria')
        ->and($list->page['quantidadeTotalItens'])->toBe(1);

    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://boleto.api.itau.com/boleto/v1/boletos'));
});

it('Boletos Negociados: cria boleto (POST /boleto/v1/boletos)', function () {
    Http::fake(['*' => Http::response(['data' => ['idBoleto' => 'BNEW', 'situacao' => 'aberto']])]);

    $res = Itau::make()->boletoNegociado(itauNovoProd())->criarBoleto(['seuNumero' => '123']);

    expect($res)->toBeInstanceOf(BoletoNegociado::class)->and($res->idBoleto)->toBe('BNEW');
    Http::assertSent(fn ($r) => $r->method() === 'POST' && str_ends_with($r->url(), '/boleto/v1/boletos'));
});

it('Boletos Negociados: consulta ativos financeiros (recebíveis)', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                ['numeroIdentificacaoAtivoFinanceiro' => 'ATIV001', 'tipoAtivoFinanceiro' => 'DUPLICATA', 'pessoaTitular' => ['cpfCnpj' => '123']],
            ],
        ]),
    ]);

    $list = Itau::make()->boletoNegociado(itauNovoProd())->ativosFinanceiros(['tipoAtivoFinanceiro' => 'DUPLICATA']);

    expect($list)->toBeInstanceOf(AtivosFinanceirosList::class)
        ->and($list->itens[0]->numeroIdentificacaoAtivoFinanceiro)->toBe('ATIV001')
        ->and($list->itens[0]->pessoaTitular['cpfCnpj'])->toBe('123');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/boleto/v1/ativos-financeiros'));
});

// ---- Wiring ---------------------------------------------------------------

it('fachada expõe conciliação e boletos negociados', function () {
    $i = itauNovoProd();

    expect(Itau::make()->conciliacao($i))->toBeInstanceOf(ConciliacaoMethods::class)
        ->and(Itau::make()->boletoNegociado($i))->toBeInstanceOf(BoletoNegociadoMethods::class);
});
