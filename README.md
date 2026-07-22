# sistem-atc/bank-sdk

SDK PHP unificado de integração bancária — **Bradesco** e **Itaú**. Segue o
mesmo molde do [`sistem-atc/marketplace-sdk`](https://github.com/sistem-atc/marketplace-sdk):
entrypoint enum, grupos de métodos por domínio, DTOs tipados e credenciais
fornecidas pelo host via contract.

Cobre Open Banking (OAuth2 client_credentials + mTLS), consulta de extrato,
cobrança (boleto e Pix), recebimentos Pix (arranjo regulatório Bacen),
pagamentos (SISPAG) e CNAB 240/400.

## Instalação

```bash
composer require sistem-atc/bank-sdk
```

Laravel 10–13. O `BanksServiceProvider` é auto-descoberto e publica o config:

```bash
php artisan vendor:publish --tag=banks-config
```

## Uso

O entrypoint é o enum `Bank`, encadeado a partir do case:

```php
use SistemAtc\Banks\Bank;

// Autenticação (client_credentials + mTLS) — devolve o AuthToken vigente
$token = Bank::Itau->auth($integration);

// Extrato (conciliação)
$eventos = Bank::Itau->statement($integration)->periodo('2026-07-01', '2026-07-31');

// Pagamento Pix de saída (SISPAG)
$res = Bank::Itau->pix($integration)->pagar([
    'valor_pagamento' => '1260.00',
    'data_pagamento'  => '2026-07-22',
    'chave'           => 'maria_pix@gmail.com',
]);
```

Trocar `Bank::Itau` por `Bank::Bradesco` não muda o código do consumidor nos
domínios comuns — cada método é tipado pela interface de domínio, não pelo banco.

## Credenciais — o contract `BankIntegration`

O SDK **não** guarda credenciais. O host (ex.: o ERP) implementa
`SistemAtc\Banks\Contracts\BankIntegration`, entregando por request:

- `client_id` / `client_secret` do app no portal do banco;
- o **certificado mTLS** (`getCertificate(): ?ClientCertificate`) — PEM
  `.crt`+`.key` (Itaú, "certificado dinâmico") ou PKCS#12 `.pfx` (Bradesco);
- flags de ambiente e persistência do `access_token`.

Multiempresa é nativo: cada CNPJ tem seu app e seu certificado, e cada chamada
carrega a integração da empresa dona da operação.

### Autenticação

- **Itaú** — `client_credentials` sobre mTLS, token no STS
  (`sts.itau.com.br`), válido ~300s. Suporta os dois métodos de client-auth:
  `client_secret` (default) e `private_key_jwt` (client_assertion RS256).
  Headers obrigatórios (`x-itau-apikey`, `x-itau-correlationID`) são injetados
  pelo SDK.
- **Bradesco** — `client_credentials` (Basic), com hook pra `client_assertion`
  JWT quando a API exigir.

## Domínios

| Domínio | Itaú | Bradesco |
|---|---|---|
| Auth (OAuth2 + mTLS) | ✅ | ✅ |
| Extrato / Saldo | ✅ | ✅ |
| Pagamentos / Pix saída | ✅ | ✅ |
| Boletos cobrança | ✅ | ✅ |
| Recebimentos Pix (Bacen COB/COBV/PIX/LOC/WEBHOOK) | ✅ | ✅ |
| Pix Automático (recorrência + QR) | ✅ | — |
| Bolecode Pix | ✅ | — |
| Saque/Troco Pix | ✅ | — |
| Cobrança QR Code (boleto híbrido) | — | ✅ |
| Débito veicular (SP/MG/PR/BA) | — | ✅ |
| Arrecadação (contas de consumo e tributos) | — | ✅ |
| TED | — | ✅ |
| Ágora Investimentos (somente leitura) | — | ✅ |
| CNAB 240/400 (remessa/retorno) | compartilhado | compartilhado |

> As APIs do Itaú vivem em hosts distintos por produto
> (`api.itau.com.br`, `pix-pj.api.itau.com`, `account-statement.api.itau.com`…);
> o SDK resolve o host por produto e ambiente.

## CNAB

Módulo compartilhado (FEBRABAN posicional): `CnabType` (240/400),
`LayoutInterface` pluggável por banco, `RetornoParser` (extração posicional) e
`RemessaBuilder`.

## Testes

```bash
composer install
vendor/bin/pest
```

## Arquitetura

```
src/
  Bank.php                 entrypoint (enum) → BankConnector
  Contracts/               BankIntegration, BankConnector, DTOInterface, Endpoints/*
  Common/                  AutoHydrate, CastToArray, attributes, HttpMethod
  Support/                 AuthToken, ClientCertificate, MtlsOptions, PrivateKeyJwt
  Exceptions/              BankAuthenticationException, BankRequestException
  Itau/  Bradesco/         Support (OAuth/HttpClientFactory/TokenRefresher),
                           Bases/BaseMethods, Endpoints/*, DTO/Response/*
  Cnab/                    CnabType, Layout, Remessa, Retorno, DTO
```

## Licença

MIT.
