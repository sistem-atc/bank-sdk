<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Banks\Itau\Itau;
use SistemAtc\Banks\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\Boletos\BoletosConsultaMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\Boletos\BoletosMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\PixAutomatico\QrCodeMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\RecebimentosPix\CobrancaImediataMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\SaqueTroco\PontosAtendimentoMethods;
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

    expect(Itau::make()->boletos($i)->emissao())->toBeInstanceOf(BoletosMethods::class)
        ->and(Itau::make()->boletos($i)->consulta())->toBeInstanceOf(BoletosConsultaMethods::class)
        ->and(Itau::make()->recebimentosPix($i)->cobImediata())->toBeInstanceOf(CobrancaImediataMethods::class)
        ->and(Itau::make()->pixAutomatico($i)->qrCode())->toBeInstanceOf(QrCodeMethods::class)
        ->and(Itau::make()->bolecode($i))->toBeInstanceOf(BolecodeMethods::class)
        ->and(Itau::make()->saqueTroco($i)->pontos())->toBeInstanceOf(PontosAtendimentoMethods::class);
});

it('resolve o HOST correto de cada produto (nao ha host unico no Itau)', function () {
    $i = itauProd();
    Http::fake(['*' => Http::response(['data' => []])]);

    // Extrato tem subdominio proprio.
    Itau::make()->statement($i)->saldos();
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://account-statement.api.itau.com/'));

    // Recebimentos Pix (regulatorio Bacen) roda no pix-pj.
    Itau::make()->recebimentosPix($i)->cobImediata()->listar();
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://pix-pj.api.itau.com/regulatorio-pix/v2/cob'));

    // Consulta de boleto fica num host distinto da emissao.
    Itau::make()->boletos($i)->consulta()->consultarDetalhe('1', '109', '00000001');
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://secure.api.cloud.itau.com.br/boletoscash/v2'));

    // SISPAG permanece no host padrao.
    Itau::make()->payments($i)->listar();
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://api.itau.com.br/sispag/v1/pagamentos_sispag'));

    // QR Code do Pix Automatico tem host proprio, diferente da recorrencia.
    Itau::make()->pixAutomatico($i)->qrCode()->consultar('abc');
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://recebimentos-pix.api.itau.com/'));
});

it('produto exclusivo do Itau nem existe no connector de outro banco', function () {
    expect(method_exists(Bradesco::class, 'recebimentosPix'))->toBeFalse()
        ->and(method_exists(Itau::class, 'recebimentosPix'))->toBeTrue();
});

it('recusa DDA no Itau em vez de chutar um endpoint inexistente', function () {
    expect(fn () => Itau::make()->dda(itauProd()))
        ->toThrow(BadMethodCallException::class, 'não há API de DDA');
});
