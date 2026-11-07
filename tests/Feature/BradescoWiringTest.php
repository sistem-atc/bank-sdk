<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Banks\Itau\Itau;
use SistemAtc\Banks\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\Agora\PosicaoMethods;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\Arrecadacao\ArrecadacaoMethods;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\DebitoVeicular\DebitoVeicularSpMethods;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\Cobranca\CobrancaWebhookMethods;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\PixQrCode\CobrancaImediataMethods;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\Ted\TedMethods;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function bradescoProd(): FakeBankIntegration
{
    config()->set('banks.sandbox', false);

    return new FakeBankIntegration(sandbox: false);
}

function bradescoTokens(): array
{
    return [
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T_OPEN', 'expires_in' => 3600]),
        '*/v2/oauth/token' => Http::response(['access_token' => 'T_PIX', 'expires_in' => 3600]),
    ];
}

it('expoe os produtos do Bradesco pela fachada do enum', function () {
    Http::fake(bradescoTokens());
    $i = bradescoProd();

    expect(Bradesco::make()->cobranca($i)->webhook())->toBeInstanceOf(CobrancaWebhookMethods::class)
        ->and(Bradesco::make()->pixQrCode($i)->cobrancaImediata())->toBeInstanceOf(CobrancaImediataMethods::class)
        ->and(Bradesco::make()->arrecadacao($i))->toBeInstanceOf(ArrecadacaoMethods::class)
        ->and(Bradesco::make()->ted($i))->toBeInstanceOf(TedMethods::class)
        ->and(Bradesco::make()->debitoVeicular($i)->sp())->toBeInstanceOf(DebitoVeicularSpMethods::class)
        ->and(Bradesco::make()->agora($i)->posicao())->toBeInstanceOf(PosicaoMethods::class);
});

it('as classes reais satisfazem os contratos cross-bank (intercambiavel com o Itau)', function () {
    Http::fake(bradescoTokens());
    $i = bradescoProd();

    expect(Bradesco::make()->statement($i))->toBeInstanceOf(StatementEndpoint::class)
        ->and(Bradesco::make()->pix($i))->toBeInstanceOf(PixEndpoint::class)
        ->and(Bradesco::make()->payments($i))->toBeInstanceOf(PaymentsEndpoint::class);
});

it('roteia cada familia para o host e o autorizador corretos', function () {
    Http::fake(bradescoTokens() + ['*' => Http::response([])]);
    $i = bradescoProd();

    // Família OPEN_API -> host openapi + autorizador server-mtls.
    Bradesco::make()->ted($i)->consultar(123, '21.11.2024');
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://openapi.bradesco.com.br/transferencia/ted/v1/consulta'));
    Http::assertSent(fn ($r) => str_contains($r->url(), '/auth/server-mtls/v2/token'));

    // Família PIX -> host qrpix + autorizador /v2/oauth/token.
    Bradesco::make()->pixQrCode($i)->cobrancaImediata()->listar([]);
    Http::assertSent(fn ($r) => str_starts_with($r->url(), 'https://qrpix.bradesco.com.br/v2/cob'));
    Http::assertSent(fn ($r) => str_contains($r->url(), 'qrpix.bradesco.com.br/v2/oauth/token'));
});

it('produto exclusivo do Bradesco nem existe no connector de outro banco', function () {
    expect(method_exists(Itau::class, 'arrecadacao'))->toBeFalse()
        ->and(method_exists(Bradesco::class, 'arrecadacao'))->toBeTrue();
});
