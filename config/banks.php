<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bank SDK
|--------------------------------------------------------------------------
|
| Config do pacote de integração bancária. As CREDENCIAIS (client_id,
| client_secret, certificado mTLS .pfx) NÃO moram aqui — vêm por request via
| o contract BankIntegration, que o host implementa reusando o cofre/cert da
| empresa (multiempresa, cada CNPJ tem seu app no banco). Aqui ficam só os
| endpoints e parâmetros de transporte, que são do BANCO, não da empresa.
|
| Ambiente: `sandbox=true` roteia pras URLs de homologação. As URLs reais
| devem ser confirmadas no portal de cada banco antes do go-live — as de
| produção exigem mTLS (certificado ICP-Brasil) na conexão.
|
*/

return [

    // Liga o modo homologação globalmente. Pode ser sobrescrito por banco.
    'sandbox' => env('BANKS_SANDBOX', true),

    // Timeouts de transporte (segundos), aplicáveis a todos os bancos.
    'http' => [
        'timeout' => (int) env('BANKS_HTTP_TIMEOUT', 30),
        'connect_timeout' => (int) env('BANKS_HTTP_CONNECT_TIMEOUT', 10),
    ],

    'bradesco' => [
        // OAuth2: Bradesco usa client_credentials com JWT assertion (private_key
        // do app) — o grant vai no mesmo host da API.
        'base_url' => [
            'production' => env('BRADESCO_BASE_URL', 'https://openapi.bradesco.com.br'),
            'sandbox' => env('BRADESCO_BASE_URL_SANDBOX', 'https://proxy.api.prebanco.com.br'),
        ],
        'oauth_path' => env('BRADESCO_OAUTH_PATH', '/auth/server/v1.1/token'),
        // Margem (s) antes do expires_at pra tratar o token como expirado.
        'token_safety_margin' => (int) env('BRADESCO_TOKEN_MARGIN', 60),
    ],

    'itau' => [
        // OAuth2: Itaú usa client_credentials + mTLS. O token vem de um host de
        // STS separado da API de negócio.
        'base_url' => [
            'production' => env('ITAU_BASE_URL', 'https://api.itau.com.br'),
            'sandbox' => env('ITAU_BASE_URL_SANDBOX', 'https://sandbox.devportal.itau.com.br'),
        ],
        'oauth_url' => [
            'production' => env('ITAU_OAUTH_URL', 'https://sts.itau.com.br/api/oauth/token'),
            'sandbox' => env('ITAU_OAUTH_URL_SANDBOX', 'https://sandbox.devportal.itau.com.br/api/oauth/token'),
        ],
        'token_safety_margin' => (int) env('ITAU_TOKEN_MARGIN', 60),
    ],

];
