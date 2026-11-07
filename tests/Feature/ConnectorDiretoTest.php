<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Banks\Bradesco\Endpoints\Ted\TedMethods;
use SistemAtc\Banks\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Banks\Itau\Itau;
use SistemAtc\Banks\Gateways\PagBrasil\Endpoints\Orders\OrderMethods;
use SistemAtc\Banks\Gateways\PagBrasil\PagBrasil;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

/** Integração com token válido: o connector não tenta autenticar no banco. */
function integracaoAutenticada(): FakeBankIntegration
{
    // Bradesco guarda o token no cache por família: simula só o endpoint de
    // token e barra qualquer outra chamada real à rede.
    Http::preventStrayRequests();
    Http::fake(['*token*' => Http::response(['access_token' => 'TOK', 'expires_in' => 3600])]);

    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

it('expõe os produtos específicos direto no connector concreto', function () {
    $i = integracaoAutenticada();

    expect(Itau::make()->bolecode($i))->toBeInstanceOf(BolecodeMethods::class)
        ->and(Bradesco::make()->ted($i))->toBeInstanceOf(TedMethods::class)
        ->and(PagBrasil::make()->pedidos(new FakePagBrasilIntegration()))->toBeInstanceOf(OrderMethods::class);
});

it('o código FEBRABAN vem do connector', function () {
    expect(Itau::make()->code())->toBe('341')
        ->and(Bradesco::make()->code())->toBe('237')
        ->and(Bank::Itau->code())->toBe(Itau::make()->code());
});

it('resolve o case a partir do valor persistido', function () {
    expect(Bank::from('itau'))->toBe(Bank::Itau)
        ->and(Bank::from('bradesco')->connector())->toBeInstanceOf(Bradesco::class)
        ->and(Bank::tryFrom('nubank'))->toBeNull();
});

it('o enum só identifica e resolve: produtos e domínios ficam no connector', function () {
    foreach (['statement', 'pix', 'payments', 'dda', 'auth', 'boletos', 'ted', 'bolecode'] as $metodo) {
        expect(method_exists(Bank::class, $metodo))->toBeFalse();
    }

    $i = integracaoAutenticada();

    expect(Bank::Itau->connector()->statement($i))->toBeInstanceOf(StatementEndpoint::class)
        ->and(Bank::Bradesco->connector()->statement($i))->toBeInstanceOf(StatementEndpoint::class);
});
