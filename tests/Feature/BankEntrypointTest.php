<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;
use SistemAtc\Banks\Itau\Itau;
use SistemAtc\Banks\Support\AuthToken;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

it('resolve o connector concreto a partir do case do enum', function () {
    expect(Bank::Bradesco->connector())->toBeInstanceOf(Bradesco::class)
        ->and(Bank::Itau->connector())->toBeInstanceOf(Itau::class)
        ->and(Bank::Bradesco->code())->toBe('237')
        ->and(Bank::Itau->code())->toBe('341');
});

it('autentica no Bradesco via client_credentials e devolve AuthToken', function () {
    Http::fake([
        '*/auth/server/v1.1/token' => Http::response([
            'access_token' => 'TOKEN_BRADESCO',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]),
    ]);

    $token = Bank::Bradesco->auth(new FakeBankIntegration());

    expect($token)->toBeInstanceOf(AuthToken::class)
        ->and($token->accessToken)->toBe('TOKEN_BRADESCO')
        ->and($token->isExpired(now: time()))->toBeFalse();

    // Basic auth com client_id/secret foi enviado ao grant.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/auth/server/v1.1/token')
        && $r['grant_type'] === 'client_credentials'
        && $r->hasHeader('Authorization'));
});

it('autentica no Itau usando o host STS separado (nao a base_url da API)', function () {
    Http::fake([
        '*sandbox.devportal.itau.com.br/api/oauth/token' => Http::response([
            'access_token' => 'TOKEN_ITAU',
            'expires_in' => 300,
        ]),
    ]);

    $token = Bank::Itau->auth(new FakeBankIntegration());

    expect($token->accessToken)->toBe('TOKEN_ITAU');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'itau.com.br/api/oauth/token')
        && $r['client_id'] === 'cli'
        && $r['client_secret'] === 'sec');
});

it('encadeia Bank::Bradesco->dda() reautenticando e retornando DTOs tipados', function () {
    Http::fake([
        '*/auth/server/v1.1/token' => Http::response(['access_token' => 'T', 'expires_in' => 3600]),
        '*/dda/v1/boletos*' => Http::response([
            'boletos' => [
                ['linhaDigitavel' => '237...', 'valorNominal' => 150.5, 'situacao' => 'aberto'],
                ['linhaDigitavel' => '341...', 'valorNominal' => 90.0, 'situacao' => 'aberto'],
            ],
        ]),
    ]);

    $endpoint = Bank::Bradesco->dda(new FakeBankIntegration());
    expect($endpoint)->toBeInstanceOf(DdaEndpoint::class);

    $boletos = $endpoint->consultar(['vencimento_de' => '2026-07-01']);

    expect($boletos)->toHaveCount(2)
        ->and($boletos[0]->valorNominal)->toBe(150.5)
        ->and($boletos[1]->situacao)->toBe('aberto');

    // Bearer token obtido no client_credentials foi anexado à chamada de negócio.
    Http::assertSent(fn ($r) => str_contains($r->url(), '/dda/v1/boletos')
        && $r->hasHeader('Authorization', 'Bearer T'));
});

it('reaproveita o token vigente sem reautenticar quando ainda valido', function () {
    Http::fake([
        '*/dda/v1/boletos*' => Http::response(['boletos' => []]),
        '*/auth/server/v1.1/token' => Http::response(['access_token' => 'NAO_DEVERIA', 'expires_in' => 3600]),
    ]);

    $integration = new FakeBankIntegration();
    $integration->accessToken = 'JA_VALIDO';
    $integration->tokenExpiresAt = time() + 3600;

    Bank::Bradesco->dda($integration)->consultar();

    // Nao houve chamada ao grant: o token em mãos ainda valia.
    Http::assertNotSent(fn ($r) => str_contains($r->url(), '/auth/server/v1.1/token'));
});
