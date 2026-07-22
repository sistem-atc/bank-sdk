<?php

declare(strict_types=1);

use SistemAtc\Banks\Support\ClientCertificate;
use SistemAtc\Banks\Support\MtlsOptions;
use SistemAtc\Banks\Tests\Fakes\FakeBankIntegration;

it('anexa cert E ssl_key separados no PEM do Itau (certificado dinamico)', function () {
    $integration = new FakeBankIntegration(
        certificate: new ClientCertificate(
            certPath: '/certs/itau/certificado.crt',
            keyPath: '/certs/itau/chave.key',
        ),
    );

    $opts = MtlsOptions::forIntegration($integration);

    // Sem ssl_key o handshake mTLS do sts.itau.com.br falha (cURL sem a chave).
    expect($opts)->toHaveKeys(['cert', 'ssl_key'])
        ->and($opts['cert'])->toBe('/certs/itau/certificado.crt')
        ->and($opts['ssl_key'])->toBe('/certs/itau/chave.key')
        ->and($opts)->not->toHaveKey('curl');
});

it('passa a passphrase junto da chave PEM quando ha senha', function () {
    $integration = new FakeBankIntegration(
        certificate: new ClientCertificate(
            certPath: '/certs/itau/certificado.crt',
            keyPath: '/certs/itau/chave.key',
            password: 'segredo',
        ),
    );

    $opts = MtlsOptions::forIntegration($integration);

    expect($opts['ssl_key'])->toBe(['/certs/itau/chave.key', 'segredo']);
});

it('usa container P12 (sem ssl_key) no PKCS#12 do Bradesco', function () {
    $integration = new FakeBankIntegration(
        certificate: new ClientCertificate(
            certPath: '/certs/bradesco/empresa.pfx',
            password: 'senha-pfx',
        ),
    );

    $opts = MtlsOptions::forIntegration($integration);

    expect($opts['cert'])->toBe(['/certs/bradesco/empresa.pfx', 'senha-pfx'])
        ->and($opts['curl'])->toBe([CURLOPT_SSLCERTTYPE => 'P12'])
        ->and($opts)->not->toHaveKey('ssl_key');
});

it('devolve vazio quando nao ha certificado (sandbox sem mTLS)', function () {
    expect(MtlsOptions::forIntegration(new FakeBankIntegration()))->toBe([]);
});
