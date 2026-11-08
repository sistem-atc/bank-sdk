<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use SistemAtc\Banks\Webhooks\Receivers\InterWebhookReceiver;

beforeEach(fn () => config(['app.url' => 'https://erp.exemplo.com.br']));

it('o pacote traz a CA do Inter válida, com intermediária e raiz', function () {
    $pem = file_get_contents((new InterWebhookReceiver())->clientCaPath());

    preg_match_all('/-----BEGIN CERTIFICATE-----.+?-----END CERTIFICATE-----/s', $pem, $blocos);
    $cns = array_map(fn ($b) => trim(openssl_x509_parse($b)['subject']['CN']), $blocos[0]);

    expect($cns)->toBe(['API Intermediate Certificate Authority', 'SSSS Root Certificate Authority']);
});

it('imprime a configuração do nginx do Inter com a rota, o cabeçalho e o upstream', function () {
    $this->artisan('banks:webhooks-nginx', ['bank' => 'inter', '--upstream' => 'http://10.0.0.5:8081'])
        ->expectsOutputToContain('ssl_client_certificate /etc/nginx/certs/inter-webhook-ca.crt;')
        ->expectsOutputToContain('location /api/webhooks/banks/inter/ {')
        ->expectsOutputToContain('proxy_pass http://10.0.0.5:8081;')
        ->expectsOutputToContain('proxy_set_header X-SSL-Client-Verify $ssl_client_verify;')
        ->expectsOutputToContain('proxy_set_header X-SSL-Client-Verify "";')
        ->expectsOutputToContain('https://erp.exemplo.com.br/api/webhooks/banks/inter/0/teste')
        ->expectsOutputToContain('API Intermediate Certificate Authority: válido até')
        ->assertSuccessful();
});

it('segue o prefixo e o cabeçalho configurados', function () {
    config([
        'banks.webhooks.prefix' => 'hooks/bancos',
        'banks.webhooks.inter.client_verify_header' => 'X-Client-Cert',
    ]);

    $this->artisan('banks:webhooks-nginx', ['--ca-dir' => '/opt/certs/'])
        ->expectsOutputToContain('ssl_client_certificate /opt/certs/inter-webhook-ca.crt;')
        ->expectsOutputToContain('location /hooks/bancos/inter/ {')
        ->expectsOutputToContain('proxy_set_header X-Client-Cert $ssl_client_verify;')
        ->expectsOutputToContain('Troque http://<app>')
        ->assertSuccessful();
});

it('com --ca imprime só o certificado, pronto pra encadear num tee', function () {
    Artisan::call('banks:webhooks-nginx', ['bank' => 'inter', '--ca' => true]);

    expect(Artisan::output())->toBe(file_get_contents((new InterWebhookReceiver())->clientCaPath()));
});

it('recusa banco sem rota de webhook', function () {
    $this->artisan('banks:webhooks-nginx', ['bank' => 'bradesco'])
        ->expectsOutputToContain('Banco sem rota de webhook no SDK: bradesco.')
        ->assertFailed();
});
