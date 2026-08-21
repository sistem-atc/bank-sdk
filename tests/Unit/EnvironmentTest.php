<?php

declare(strict_types=1);

use SistemAtc\Banks\Itau\Support\ItauHosts;
use SistemAtc\Banks\Itau\Support\OAuth;
use SistemAtc\Banks\Support\Environment;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

it('a integracao decide o ambiente quando o kill-switch nao foi ligado', function () {
    // Regressão: o default de banks.sandbox era `true`, então uma integração
    // marcada como PRODUÇÃO ia parar no sandbox em silêncio — só não ia se o
    // host declarasse BANKS_SANDBOX=false. Produção não pode depender de uma
    // variável de ambiente ESTAR PRESENTE.
    config()->offsetUnset('banks.sandbox');

    expect(Environment::forIntegration(new FakeBankIntegration(sandbox: false)))
        ->toBe(Environment::PRODUCTION)
        ->and(Environment::forIntegration(new FakeBankIntegration(sandbox: true)))
        ->toBe(Environment::SANDBOX);
});

it('o default publicado do pacote deixa a integracao mandar', function () {
    expect(config('banks.sandbox'))->toBeFalse();

    expect(Environment::forIntegration(new FakeBankIntegration(sandbox: false)))
        ->toBe(Environment::PRODUCTION);
});

it('o kill-switch global force TODAS as integracoes pro sandbox quando ligado', function () {
    config()->set('banks.sandbox', true);

    expect(Environment::forIntegration(new FakeBankIntegration(sandbox: false)))
        ->toBe(Environment::SANDBOX);
});

it('integracao em sandbox nunca vaza pra producao', function () {
    config()->set('banks.sandbox', false);

    expect(Environment::forIntegration(new FakeBankIntegration(sandbox: true)))
        ->toBe(Environment::SANDBOX);
});

it('o roteamento de URL do Itau segue a integracao sem depender do .env', function () {
    config()->offsetUnset('banks.sandbox');

    $prod = new FakeBankIntegration(sandbox: false);

    expect(OAuth::tokenUrl($prod))->toBe('https://sts.itau.com.br/api/oauth/token')
        ->and(ItauHosts::resolve('account_statement', $prod))
        ->toBe('https://account-statement.api.itau.com');

    $sandbox = new FakeBankIntegration(sandbox: true);

    expect(OAuth::tokenUrl($sandbox))->toContain('/sandbox/');
});
