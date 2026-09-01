<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Bank;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

function itauRetryIntegration(): FakeBankIntegration
{
    $i = new FakeBankIntegration();
    $i->accessToken = 'TOK';
    $i->tokenExpiresAt = time() + 300;

    return $i;
}

/**
 * O Itau publica cada produto num subdominio proprio. O retry de 401/403 do
 * BaseMethods RECRIA o http client — e, sem repassar o baseUrl do produto, a
 * segunda tentativa caia no host DEFAULT (api.itau.com.br).
 *
 * Efeito pratico (diagnosticado no extrato em 01/09/2026): a 1a tentativa batia
 * no host certo e voltava 403 (sem permissao), a 2a ia pro host errado e voltava
 * 404. O chamador recebia "endpoint nao existe" e o problema real — PERMISSAO —
 * ficava escondido. Foram horas de investigacao no lugar errado.
 *
 * Os testes que ja' existiam nao pegavam isso: casam a URL com wildcard
 * (`*​/account-statement/v1/...`), entao passam com QUALQUER host.
 */
it('o retry de 403 mantem o host do produto, e nao cai no host default', function () {
    $integration = itauRetryIntegration();

    // Hosts DISTINTOS de proposito: no sandbox real o host do produto e o
    // default sao o mesmo (api.itau.com.br/sandbox), entao o teste passaria
    // mesmo com o bug. Separando os dois, trocar de host fica visivel.
    config()->set('banks.itau.hosts.account_statement.sandbox', 'https://statement-host.test');
    config()->set('banks.itau.hosts.default.sandbox', 'https://default-host.test');

    $chamadas = [];

    Http::fake(function ($request) use (&$chamadas) {
        $chamadas[] = $request->url();

        // Sempre 403: forca o retry e deixa o erro final ser o REAL.
        return Http::response(['message' => 'Forbidden'], 403);
    });

    try {
        Bank::Itau->statement($integration)->saldos();
    } catch (\Throwable) {
        // O que importa aqui e' PRA ONDE as chamadas foram.
    }

    expect($chamadas)->toHaveCount(2, 'deveria tentar 2x (original + retry)');

    foreach ($chamadas as $i => $url) {
        // Ambas as chamadas (original + retry) tem que bater no host do PRODUTO.
        expect($url)->toContain('statement-host.test')
            ->and($url)->not->toContain('default-host.test');
    }
});

it('o erro que chega ao chamador e o 403 real, nao um 404 do host errado', function () {
    $integration = itauRetryIntegration();

    Http::fake([
        '*' => Http::response(['message' => 'Forbidden'], 403),
    ]);

    $status = null;

    try {
        Bank::Itau->statement($integration)->saldos();
    } catch (\SistemAtc\Banks\Exceptions\BankRequestException $e) {
        $status = $e->status();
    }

    // 403 diz "sem permissao" (acao: liberar o produto no portal do banco).
    // 404 mandaria o time cacar endpoint errado, que foi o que aconteceu.
    expect($status)->toBe(403);
});
