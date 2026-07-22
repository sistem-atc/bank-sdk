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
        // Autenticação: "Modelo de autenticação MTLS" do Bradesco Developers —
        // credencial client_id/client_secret emitida no portal, sobre TLS mútuo.
        // O certificado registrado é a chave PÚBLICA (sandbox: autoassinado;
        // produção: A1 de Autoridade Certificadora, .pem/.cer/.crt); a chave
        // privada fica com a aplicação. Ou seja, o mTLS é par PEM cert+key —
        // mesmo formato do certificado dinâmico do Itaú (ver ClientCertificate).
        //
        // URLs confirmadas no guia "Primeiros passos" do portal.
        'base_url' => [
            'production' => env('BRADESCO_BASE_URL', 'https://openapi.bradesco.com.br'),
            'sandbox' => env('BRADESCO_BASE_URL_SANDBOX', 'https://openapisandbox.prebanco.com.br'),
            // Homologação é um 3º ambiente, distinto do sandbox.
            'homologacao' => env('BRADESCO_BASE_URL_HOMOLOG', 'https://proxy.api.prebanco.com.br'),
        ],
        'oauth_path' => env('BRADESCO_OAUTH_PATH', '/auth/server/v1.1/token'),
        // Margem (s) antes do expires_at pra tratar o token como expirado.
        'token_safety_margin' => (int) env('BRADESCO_TOKEN_MARGIN', 60),

        // Como no Itaú, os produtos NÃO compartilham um host único: as Open APIs
        // (Arrecadação, Cobrança, Cobrança Híbrida, Débito de Veículos, Débito
        // Automático, Saldo/Extrato, Pagamentos e TED) ficam no openapi, e as
        // APIs Pix num host próprio (qrpix).
        'hosts' => [
            'default' => [ // Open APIs
                'production' => env('BRADESCO_HOST', 'https://openapi.bradesco.com.br'),
                'sandbox' => env('BRADESCO_HOST_SANDBOX', 'https://openapisandbox.prebanco.com.br'),
            ],
            'pix' => [ // APIs Pix
                'production' => env('BRADESCO_HOST_PIX', 'https://qrpix.bradesco.com.br'),
                'sandbox' => env('BRADESCO_HOST_PIX_SANDBOX', 'https://qrpix-h.bradesco.com.br'),
            ],
        ],
    ],

    'itau' => [
        // OAuth2 client_credentials + mTLS. O token vem de um host STS separado
        // da API de negócio. base_url é só o HOST — cada API prefixa seu path
        // (ex.: SISPAG usa /sispag/v1/...); em sandbox o host já embute /sandbox.
        'base_url' => [
            'production' => env('ITAU_BASE_URL', 'https://api.itau.com.br'),
            'sandbox' => env('ITAU_BASE_URL_SANDBOX', 'https://api.itau.com.br/sandbox'),
        ],
        'oauth_url' => [
            'production' => env('ITAU_OAUTH_URL', 'https://sts.itau.com.br/api/oauth/token'),
            'sandbox' => env('ITAU_OAUTH_URL_SANDBOX', 'https://api.itau.com.br/sandbox/api/oauth/token'),
        ],
        // Método de autenticação do cliente OAuth: 'client_secret' (default, do
        // fluxo do certificado dinâmico) ou 'private_key_jwt' (client_assertion
        // RS256). Pode ser sobrescrito por integração em settings['auth_method'].
        'auth_method' => env('ITAU_AUTH_METHOD', 'client_secret'),
        'token_safety_margin' => (int) env('ITAU_TOKEN_MARGIN', 60),

        // As APIs do Itaú NÃO compartilham um host único — cada PRODUTO vive num
        // subdomínio próprio (confirmado na doc oficial de cada API). O connector
        // resolve o host por produto+ambiente (ver Support/ItauHosts). Hosts de
        // produção são os documentados; sandbox é env-overridável (os prefixos de
        // homologação variam e se fecham quando o app real for configurado).
        'hosts' => [
            'default' => [ // SISPAG (Cash Management) + Boletos emissão/instrução
                'production' => env('ITAU_HOST', 'https://api.itau.com.br'),
                'sandbox' => env('ITAU_HOST_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
            'account_statement' => [ // Extrato
                'production' => env('ITAU_HOST_STATEMENT', 'https://account-statement.api.itau.com'),
                'sandbox' => env('ITAU_HOST_STATEMENT_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
            'boletos_consulta' => [ // Boletos - consulta de detalhe
                'production' => env('ITAU_HOST_BOLETOS_CONSULTA', 'https://secure.api.cloud.itau.com.br'),
                'sandbox' => env('ITAU_HOST_BOLETOS_CONSULTA_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
            'boletos_extrato' => [ // Boletos - extrato cobrança
                'production' => env('ITAU_HOST_BOLETOS_EXTRATO', 'https://boleto.api.itau.com'),
                'sandbox' => env('ITAU_HOST_BOLETOS_EXTRATO_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
            'pix_recebimentos' => [ // Recebimentos Pix (regulatório Bacen) + Bolecode
                'production' => env('ITAU_HOST_PIX_RECEB', 'https://pix-pj.api.itau.com'),
                'sandbox' => env('ITAU_HOST_PIX_RECEB_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
            'pix_automatico_rec' => [ // Pix Automático - recorrência/cobrança
                'production' => env('ITAU_HOST_PIX_AUT_REC', 'https://pixautomatico-recebimentos.api.itau.com'),
                'sandbox' => env('ITAU_HOST_PIX_AUT_REC_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
            'pix_automatico_qr' => [ // Pix Automático - emissão de QR Code
                'production' => env('ITAU_HOST_PIX_AUT_QR', 'https://recebimentos-pix.api.itau.com'),
                'sandbox' => env('ITAU_HOST_PIX_AUT_QR_SANDBOX', 'https://api.itau.com.br/sandbox'),
            ],
        ],
    ],

];
