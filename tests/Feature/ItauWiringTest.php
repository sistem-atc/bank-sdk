<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\BoletosConsultaMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\BoletosMethods;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\QrCodeMethods;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\CobrancaImediataMethods;
use SistemAtc\Banks\Itau\Endpoints\SaqueTroco\PontosAtendimentoMethods;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

/** Integração autenticada apontando pra PRODUÇÃO (pra checar os hosts reais). */
function itauProd(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    $i = new FakeBankIntegration(sandbox: false);
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('expoe os produtos do Itau pela fachada do enum', function () {
    $i = itauProd();

    expect(Bank::Itau->boletos($i)->emissao())->toBeInstanceOf(BoletosMethods::class)
        ->and(Bank::Itau->boletos($i)->consulta())->toBeInstanceOf(BoletosConsultaMethods::class)
        ->and(Bank::Itau->recebimentosPix($i)->cobImediata())->toBeInstanceOf(CobrancaImediataMethods::class)
        ->and(Bank::Itau->pixAutomatico($i)->qrCode())->toBeInstanceOf(QrCodeMethods::class)
        ->and(Bank::Itau->bolecode($i))->toBeInstanceOf(BolecodeMethods::class)
        ->and(Bank::Itau->saqueTroco($i)->pontos())->toBeInstanceOf(PontosAtendimentoMethods::class);
});

it('resolve o HOST correto de cada produto (nao ha host unico no Itau)', function () {
    $i = itauProd();
    Http::fake(['*' => Http::response(['data' => []])]);

    // Extrato tem subdominio proprio.
    Bank::Itau->statement($i)->saldos();
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://account-statement.api.itau.com/'));

    // Recebimentos Pix (regulatorio Bacen) roda no pix-pj.
    Bank::Itau->recebimentosPix($i)->cobImediata()->listar();
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://pix-pj.api.itau.com/regulatorio-pix/v2/cob'));

    // Consulta de boleto fica num host distinto da emissao.
    Bank::Itau->boletos($i)->consulta()->consultarDetalhe('1', '109', '00000001');
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://secure.api.cloud.itau.com.br/boletoscash/v2'));

    // SISPAG permanece no host padrao.
    Bank::Itau->payments($i)->listar();
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://api.itau.com.br/sispag/v1/pagamentos_sispag'));

    // QR Code do Pix Automatico tem host proprio, diferente da recorrencia.
    Bank::Itau->pixAutomatico($i)->qrCode()->consultar('abc');
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://recebimentos-pix.api.itau.com/'));
});

it('recusa produto exclusivo do Itau quando o banco e outro', function () {
    $i = itauProd();

    expect(fn () => Bank::Bradesco->recebimentosPix($i))
        ->toThrow(BadMethodCallException::class, 'exclusivo do Itaú');
});

it('recusa DDA no Itau em vez de chutar um endpoint inexistente', function () {
    expect(fn () => Bank::Itau->dda(itauProd()))
        ->toThrow(BadMethodCallException::class, 'não há API de DDA');
});
