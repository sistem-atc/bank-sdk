<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Itau\Itau;
use SistemAtc\Banks\Support\AuthToken;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

it('resolve o connector concreto a partir do case do enum', function () {
    expect(Bank::Bradesco->connector())->toBeInstanceOf(Bradesco::class)
        ->and(Bank::Itau->connector())->toBeInstanceOf(Itau::class)
        ->and(Bank::Bradesco->code())->toBe('237')
        ->and(Bank::Itau->code())->toBe('341');
});

it('autentica no Bradesco (open_api) com credenciais NO CORPO', function () {
    Http::fake([
        '*/auth/server-mtls/v2/token' => Http::response([
            'access_token' => 'TOKEN_BRADESCO',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]),
    ]);

    $token = Bank::Bradesco->auth(new FakeBankIntegration());

    expect($token)->toBeInstanceOf(AuthToken::class)
        ->and($token->accessToken)->toBe('TOKEN_BRADESCO')
        ->and($token->isExpired(now: time()))->toBeFalse();

    // Autorizador das Open APIs: client_id/secret vão no FORM, não em Basic.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/auth/server-mtls/v2/token')
        && $r['grant_type'] === 'client_credentials'
        && $r['client_id'] === 'cli'
        && $r['client_secret'] === 'sec');
});

it('autentica no Bradesco (pix) com Basic auth no outro autorizador', function () {
    Http::fake([
        '*/v2/oauth/token' => Http::response(['access_token' => 'TOKEN_PIX', 'expires_in' => 3600]),
    ]);

    $token = \SistemAtc\Banks\Bradesco\Support\OAuth::authenticate(
        new FakeBankIntegration(),
        \SistemAtc\Banks\Bradesco\Support\BradescoHosts::FAMILY_PIX,
    );

    expect($token->accessToken)->toBe('TOKEN_PIX');

    // Família Pix: credenciais em Basic, só grant_type no corpo.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/oauth/token')
        && $r->hasHeader('Authorization')
        && $r['grant_type'] === 'client_credentials'
        && ! isset($r['client_secret']));
});

it('autentica no Itau usando o host STS separado (nao a base_url da API)', function () {
    Http::fake([
        '*api.itau.com.br/sandbox/api/oauth/token' => Http::response([
            'access_token' => 'TOKEN_ITAU',
            'expires_in' => 300,
        ]),
    ]);

    $token = Bank::Itau->auth(new FakeBankIntegration());

    expect($token->accessToken)->toBe('TOKEN_ITAU');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'itau.com.br/sandbox/api/oauth/token')
        && $r['client_id'] === 'cli'
        && $r['client_secret'] === 'sec');
});

it('encadeia Bank::Bradesco->statement() autenticando e anexando o Bearer', function () {
    Http::fake([
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/fornecimento-saldos-contas/saldos*' => Http::response(['codigoRetorno' => '0']),
    ]);

    $endpoint = Bank::Bradesco->statement(new FakeBankIntegration());
    expect($endpoint)->toBeInstanceOf(StatementEndpoint::class);

    $endpoint->saldos('1500', '0012345');

    // Bearer do client_credentials anexado à chamada de negócio.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/fornecimento-saldos-contas/saldos')
        && $r->hasHeader('Authorization', 'Bearer T'));
});

it('recusa DDA no Bradesco em vez de chutar um endpoint inexistente', function () {
    expect(fn () => Bank::Bradesco->dda(new FakeBankIntegration()))
        ->toThrow(BadMethodCallException::class, 'não há API de DDA');
});

it('autentica UMA vez e reaproveita o token em cache nas chamadas seguintes', function () {
    Http::fake([
        '*/fornecimento-saldos-contas/saldos*' => Http::response(['codigoRetorno' => '0']),
        '*/auth/server-mtls/v2/token' => Http::response(['access_token' => 'T1', 'expires_in' => 3600]),
    ]);

    $integration = new FakeBankIntegration();

    // Três chamadas de negócio seguidas.
    Bank::Bradesco->statement($integration)->saldos('1500', '0012345');
    Bank::Bradesco->statement($integration)->saldos('1500', '0012345');
    Bank::Bradesco->statement($integration)->saldos('1500', '0012345');

    // O token vive em cache POR FAMÍLIA — só o primeiro acesso vai ao autorizador.
    $grants = collect(Http::recorded())
        ->filter(fn ($pair) => str_contains($pair[0]->url(), '/auth/server-mtls/v2/token'))
        ->count();

    expect($grants)->toBe(1);
});
