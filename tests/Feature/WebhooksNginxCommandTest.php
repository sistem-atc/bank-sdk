<?php

declare(strict_types=1);

/**
 * CA autoassinada gerada na hora (o pacote não carrega certificado de banco).
 * Devolve o caminho do PEM.
 */
function caDeTeste(string $cn = 'CA de Teste', int $dias = 365): string
{
    $chave = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    $csr = openssl_csr_new(['commonName' => $cn], $chave);
    $cert = openssl_csr_sign($csr, null, $chave, $dias);
    openssl_x509_export($cert, $pem);

    $path = tempnam(sys_get_temp_dir(), 'ca').'.crt';
    file_put_contents($path, $pem);

    return $path;
}

beforeEach(fn () => config(['app.url' => 'https://erp.exemplo.com.br']));

it('confere a CA informada e imprime a configuração do nginx do Inter', function () {
    $ca = caDeTeste('API Intermediate Certificate Authority');

    $this->artisan('banks:webhooks-nginx', ['bank' => 'inter', '--ca-path' => $ca, '--upstream' => 'http://10.0.0.5:8081'])
        ->expectsOutputToContain('API Intermediate Certificate Authority: válido até')
        ->expectsOutputToContain("sudo install -D -m 644 '{$ca}' /etc/nginx/certs/inter-webhook-ca.crt")
        ->expectsOutputToContain('ssl_client_certificate /etc/nginx/certs/inter-webhook-ca.crt;')
        ->expectsOutputToContain('location /api/webhooks/banks/inter/ {')
        ->expectsOutputToContain('proxy_pass http://10.0.0.5:8081;')
        ->expectsOutputToContain('proxy_set_header X-SSL-Client-Verify $ssl_client_verify;')
        ->expectsOutputToContain('proxy_set_header X-SSL-Client-Verify "";')
        ->expectsOutputToContain('https://erp.exemplo.com.br/api/webhooks/banks/inter/0/teste')
        ->assertSuccessful();
});

it('segue o prefixo, o cabeçalho e a pasta configurados', function () {
    config([
        'banks.webhooks.prefix' => 'hooks/bancos',
        'banks.webhooks.inter.client_verify_header' => 'X-Client-Cert',
    ]);

    $this->artisan('banks:webhooks-nginx', ['--ca-path' => caDeTeste(), '--ca-dir' => '/opt/certs/'])
        ->expectsOutputToContain('ssl_client_certificate /opt/certs/inter-webhook-ca.crt;')
        ->expectsOutputToContain('location /hooks/bancos/inter/ {')
        ->expectsOutputToContain('proxy_set_header X-Client-Cert $ssl_client_verify;')
        ->expectsOutputToContain('Troque http://<app>')
        ->assertSuccessful();
});

it('avisa quando a CA está perto de vencer', function () {
    $this->artisan('banks:webhooks-nginx', ['--ca-path' => caDeTeste('CA Curta', dias: 30)])
        ->expectsOutputToContain('CA Curta: vence em')
        ->assertSuccessful();
});

it('exige o caminho da CA', function () {
    $this->artisan('banks:webhooks-nginx', ['bank' => 'inter'])
        ->expectsOutputToContain('--ca-path=<caminho do ca.crt')
        ->assertFailed();
});

it('recusa arquivo que não existe ou não é certificado', function () {
    $this->artisan('banks:webhooks-nginx', ['--ca-path' => '/nao/existe.crt'])
        ->expectsOutputToContain('Não consegui ler o certificado: /nao/existe.crt.')
        ->assertFailed();

    $lixo = tempnam(sys_get_temp_dir(), 'ca');
    file_put_contents($lixo, 'não é PEM');

    $this->artisan('banks:webhooks-nginx', ['--ca-path' => $lixo])
        ->expectsOutputToContain('não tem certificado PEM legível')
        ->assertFailed();
});

it('recusa banco sem rota de webhook', function () {
    $this->artisan('banks:webhooks-nginx', ['bank' => 'bradesco'])
        ->expectsOutputToContain('Banco sem rota de webhook no SDK: bradesco.')
        ->assertFailed();
});
