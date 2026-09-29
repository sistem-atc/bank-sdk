<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\PagBrasil\PagBrasil;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;
use SistemAtc\Banks\Tests\Fakes\FakePagBrasilIntegration;

it('resolve o connector PagBrasil e recusa os domínios de banco', function () {
    $i = new FakePagBrasilIntegration();

    expect(Bank::PagBrasil->connector())->toBeInstanceOf(PagBrasil::class)
        ->and(fn () => Bank::PagBrasil->code())->toThrow(BadMethodCallException::class, 'FEBRABAN')
        ->and(fn () => Bank::PagBrasil->statement($i))->toThrow(BadMethodCallException::class, 'Settlement Reports')
        ->and(fn () => Bank::PagBrasil->dda($i))->toThrow(BadMethodCallException::class)
        ->and(fn () => Bank::PagBrasil->pix($i))->toThrow(BadMethodCallException::class, 'payout()')
        ->and(fn () => Bank::PagBrasil->payments($i))->toThrow(BadMethodCallException::class);
});

it('produtos da PagBrasil não existem nos bancos', function () {
    expect(fn () => Bank::Itau->pedidos(new FakeBankIntegration()))->toThrow(BadMethodCallException::class, 'exclusivo da PagBrasil')
        ->and(fn () => Bank::Bradesco->pagStream(new FakeBankIntegration()))->toThrow(BadMethodCallException::class, 'exclusivo da PagBrasil')
        ->and(fn () => Bank::Bradesco->pixAutomatico(new FakeBankIntegration()))->toThrow(BadMethodCallException::class, 'exclusivo do Itaú');
});

it('exige integração com as credenciais da PagBrasil', function () {
    expect(fn () => Bank::PagBrasil->pedidos(new FakeBankIntegration()))
        ->toThrow(BankAuthenticationException::class, 'PagBrasilIntegration')
        ->and(fn () => Bank::PagBrasil->pedidos(new FakePagBrasilIntegration(pbToken: '')))
        ->toThrow(BankAuthenticationException::class, 'pbtoken')
        ->and(fn () => Bank::PagBrasil->payout(new FakePagBrasilIntegration(active: false)))
        ->toThrow(BankAuthenticationException::class, 'inativa');
});

it('produção vai pro connect.pagbrasil.com por padrão; sandbox pro sandbox', function () {
    Http::fake(['*' => Http::response('<request></request>')]);

    Bank::PagBrasil->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('1');
    Bank::PagBrasil->pedidos(new FakePagBrasilIntegration(sandbox: true))->consultar('2');

    Http::assertSent(fn (Request $r) => $r->url() === 'https://connect.pagbrasil.com/api/order/get' && $r['order'] === '1');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://sandbox.pagbrasil.com/api/order/get' && $r['order'] === '2');
});

it('env sobrescreve o host; o kill-switch global força sandbox', function () {
    config(['banks.pagbrasil.base_url.production' => 'https://api.pagbrasil.example/']);
    Http::fake(['*' => Http::response('<request></request>')]);

    Bank::PagBrasil->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('1');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://api.pagbrasil.example/api/order/get');

    config(['banks.sandbox' => true]);
    Bank::PagBrasil->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('2');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://sandbox.pagbrasil.com/api/order/get' && $r['order'] === '2');
});

it('URL zerada no config falha explícito', function () {
    config(['banks.pagbrasil.base_url.production' => '']);

    expect(fn () => Bank::PagBrasil->pedidos(new FakePagBrasilIntegration(sandbox: false))->consultar('1'))
        ->toThrow(BankAuthenticationException::class, 'PAGBRASIL_BASE_URL');
});

it('gera o token OAuth do PagBrasil.JS com Basic auth', function () {
    Http::fake(['sandbox.pagbrasil.com/api/oauth/token' => Http::response(['access_token' => 'JS_TOKEN', 'expires_in' => 600])]);

    $integration = new FakePagBrasilIntegration(clientId: 'cid', clientSecret: 'csec');
    $token = Bank::PagBrasil->auth($integration);

    expect($token->accessToken)->toBe('JS_TOKEN')
        ->and($token->isExpired(time()))->toBeFalse()
        ->and($integration->accessToken)->toBe('JS_TOKEN')
        ->and($integration->expiresIn)->toBe(600);

    Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Basic '.base64_encode('cid:csec'))
        && $r['grant_type'] === 'client_credentials');
});

it('OAuth recusado vira erro de autenticação', function () {
    Http::fake(['*/api/oauth/token' => Http::response(['error' => 'invalid_client'], 401)]);

    expect(fn () => Bank::PagBrasil->auth(new FakePagBrasilIntegration()))
        ->toThrow(BankAuthenticationException::class, 'invalid_client');
});
