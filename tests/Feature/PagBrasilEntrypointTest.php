<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Banks\Itau\Itau;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\GatewayConnector;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Gateway;
use SistemAtc\Banks\Gateways\PagBrasil\PagBrasil;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

it('PagBrasil é gateway: resolve pelo enum Gateway e não é BankConnector', function () {
    expect(Gateway::PagBrasil->connector())->toBeInstanceOf(PagBrasil::class)
        ->and(Gateway::from('pagbrasil'))->toBe(Gateway::PagBrasil)
        ->and(PagBrasil::make())->toBeInstanceOf(GatewayConnector::class)
        ->and(PagBrasil::make())->not->toBeInstanceOf(BankConnector::class)
        ->and(Bank::tryFrom('pagbrasil'))->toBeNull();
});

it('produtos da PagBrasil não existem nos bancos', function () {
    expect(method_exists(Itau::class, 'pedidos'))->toBeFalse()
        ->and(method_exists(Bradesco::class, 'pagStream'))->toBeFalse()
        ->and(method_exists(Bradesco::class, 'pixAutomatico'))->toBeFalse();
});

it('exige integração com as credenciais da PagBrasil', function () {
    expect(fn () => PagBrasil::make()->pedidos(new FakeBankIntegration()))
        ->toThrow(BankAuthenticationException::class, 'PagBrasilIntegration')
        ->and(fn () => PagBrasil::make()->pedidos(new FakePagBrasilIntegration(pbToken: '')))
        ->toThrow(BankAuthenticationException::class, 'pbtoken')
        ->and(fn () => PagBrasil::make()->payout(new FakePagBrasilIntegration(active: false)))
        ->toThrow(BankAuthenticationException::class, 'inativa');
});

it('produção vai pro connect.pagbrasil.com por padrão; sandbox pro sandbox', function () {
    Http::fake(['*' => Http::response('<request></request>')]);

    PagBrasil::make()->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('1');
    PagBrasil::make()->pedidos(new FakePagBrasilIntegration(sandbox: true))->consultar('2');

    Http::assertSent(fn (Request $r) => $r->url() === 'https://connect.pagbrasil.com/api/order/get' && $r['order'] === '1');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://sandbox.pagbrasil.com/api/order/get' && $r['order'] === '2');
});

it('env sobrescreve o host; o kill-switch global força sandbox', function () {
    config(['banks.pagbrasil.base_url.production' => 'https://api.pagbrasil.example/']);
    Http::fake(['*' => Http::response('<request></request>')]);

    PagBrasil::make()->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('1');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://api.pagbrasil.example/api/order/get');

    config(['banks.sandbox' => true]);
    PagBrasil::make()->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('2');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://sandbox.pagbrasil.com/api/order/get' && $r['order'] === '2');
});

it('URL zerada no config falha explícito', function () {
    config(['banks.pagbrasil.base_url.production' => '']);

    expect(fn () => PagBrasil::make()->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('1'))
        ->toThrow(BankAuthenticationException::class, 'PAGBRASIL_BASE_URL');
});

it('gera o token OAuth do PagBrasil.JS com Basic auth', function () {
    Http::fake(['sandbox.pagbrasil.com/api/oauth/token' => Http::response(['access_token' => 'JS_TOKEN', 'expires_in' => 600])]);

    $integration = new FakePagBrasilIntegration(clientId: 'cid', clientSecret: 'csec');
    $token = PagBrasil::make()->auth($integration);

    expect($token->accessToken)->toBe('JS_TOKEN')
        ->and($token->isExpired(time()))->toBeFalse()
        ->and($integration->accessToken)->toBe('JS_TOKEN')
        ->and($integration->expiresIn)->toBe(600);

    Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Basic '.base64_encode('cid:csec'))
        && $r['grant_type'] === 'client_credentials');
});

it('OAuth recusado vira erro de autenticação', function () {
    Http::fake(['*/api/oauth/token' => Http::response(['error' => 'invalid_client'], 401)]);

    expect(fn () => PagBrasil::make()->auth(new FakePagBrasilIntegration()))
        ->toThrow(BankAuthenticationException::class, 'invalid_client');
});
