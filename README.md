# sistem-atc/bank-sdk

SDK PHP unificado de integração bancária — **Bradesco** e **Itaú** — e do
gateway de pagamento **PagBrasil**. Segue o
mesmo molde do [`sistem-atc/marketplace-sdk`](https://github.com/sistem-atc/marketplace-sdk):
entrypoint enum, grupos de métodos por domínio, DTOs tipados e credenciais
fornecidas pelo host via contract.

Cobre Open Banking (OAuth2 client_credentials + mTLS), consulta de extrato,
cobrança (boleto e Pix), recebimentos Pix (arranjo regulatório Bacen),
pagamentos (SISPAG) e CNAB 240/400. Da PagBrasil, cobre a API inteira:
pedidos (cartão, Débito Flash, boleto, Pix), Link de Pagamento, Pix
Automático, Payout, assinaturas PagStream, IPN/webhooks e o relatório de
liquidação.

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
| Recebimentos Pix (Bacen COB/COBV/PIX/LOC/WEBHOOK + resolver QR) | ✅ | ✅ |
| Conciliação Pix (lançamentos conciliados) | ✅ | — |
| Pix Automático (recorrência + cobrança + QR) | ✅ | — |
| Bolecode Pix | ✅ | — |
| Boletos Negociados / Ativos Financeiros (recebíveis) | ✅ | — |
| Saque/Troco Pix | ✅ | — |
| Cobrança QR Code (boleto híbrido) | — | ✅ |
| Débito veicular (SP/MG/PR/BA) | — | ✅ |
| Arrecadação (contas de consumo e tributos) | — | ✅ |
| TED | — | ✅ |
| Ágora Investimentos (somente leitura) | — | ✅ |
| Gateway PagBrasil (ver seção própria) | — | — |
| CNAB 240/400 (remessa/retorno) | compartilhado | compartilhado |

> As APIs do Itaú vivem em hosts distintos por produto
> (`api.itau.com.br`, `pix-pj.api.itau.com`, `account-statement.api.itau.com`…);
> o SDK resolve o host por produto e ambiente.

## PagBrasil

A PagBrasil é gateway, não banco: mesma fachada, mas sem extrato/DDA/SISPAG
(`statement()`, `dda()`, `pix()`, `payments()` e `code()` falham com mensagem
explícita). A integração implementa `Contracts\PagBrasilIntegration`, que
soma ao `BankIntegration` o **pbtoken**, a **secret phrase** e a **signature
key** (HMAC) do Dashboard.

```php
use SistemAtc\Banks\Bank;

// Pedidos — /api/order/* (cartão, débito, boleto, Pix). Entrada tipada
// (DTO\Request\*) ou, por compatibilidade, array com os nomes da doc.
$pedido = Bank::PagBrasil->pedidos($i)->pix(new PixOrder(
    order: 'PED-123', productName: 'Whey 900g', products: [new Product('WHEY-900', 129.9)],
    customer: new Customer('José da Silva', '529.982.247-25', 'jose@x.com', '11999990000'),
    address: new Address('Av. Paulista, 100', '01311-100', 'São Paulo', 'SP'),
    amountBrl: 129.9, expirationMinutes: 30,
));
$pedido->pixCode;                                   // copia-e-cola
Bank::PagBrasil->pedidos($i)->consultar('PED-123'); // null se não existe
Bank::PagBrasil->pedidos($i)->estornar(new Refund('PED-123', '129.90', new RefundPixKey(PixKeyType::Email, 'jose@x.com')));

// Link de Pagamento, Pix Automático, Payout
Bank::PagBrasil->linkPagamento($i)->criar(['order' => 'PED-124', 'amount_brl' => '50.00'])->urlPayment;
Bank::PagBrasil->pixAutomatico($i)->consultarRecorrencia($pixRecId);
Bank::PagBrasil->payout($i)->enviar('91051605962', '150.00', 'Comissão');

// PagStream (assinaturas) — API REST v2 + as rotas que ainda são v1
Bank::PagBrasil->pagStream($i)->assinaturas()->listar(['tax_id' => '12345678909']);
Bank::PagBrasil->pagStream($i)->recorrencias()->pular('P17648804286', 'next');

// IPN / webhook — confere secret + HMAC e devolve a notificação tipada
$notificacao = Bank::PagBrasil->webhook($i)->parse($request->all());
return response(WebhookVerifier::acknowledgement()); // "Received successfully …"

// Liquidação (CSV do Dashboard; não há API) — taxas por transação
$linhas = SettlementReport::parse(file_get_contents($csv));
```

| Produto | Rotas |
|---|---|
| Pedidos | `/api/order/add`, `get`, `refund`, `extend`, `cancel`, `creditcard/delete`, `/api/pix/1click` |
| Link de Pagamento | `/api/checkout/add` |
| Pix Automático | `/api/order/add` (pix_rec), `/api/pix/rec/add`, `rec/get`, `rec/retry`, `/mock/pix/automatic/chargeRecurrences` (sandbox) |
| Payout | `/api/payout/` (addpayee, updatepayee, deletepayee, getpayee, addpayout) |
| PagStream v2 | `/api/v2/pagstream/subscriptions…`, `…/recurrences…`, `…/shippings`, `/products`, `/charges` (24 operações) |
| PagStream v1 | `/api/pagstream/subscription/add`, `subscription/item/add\|update\|delete` |
| OAuth (PagBrasil.JS) | `/api/oauth/token` — via `Bank::PagBrasil->auth($i)` |

Pontos que não estão óbvios na doc da PagBrasil:

- **HMAC sobre ISO-8859-1.** A assinatura (`signature`) é
  `hmac_md5(valores concatenados + comprimento)` sobre os bytes **Latin-1**;
  calcular sobre a string UTF-8 erra todo pedido com acento. Os 10 exemplos
  publicados na doc estão reproduzidos nos testes.
- **Lista de boletos pagos:** o HMAC é do `content` byte a byte, com CRLF.
- **Erro da API clássica vem em texto com HTTP 200** ("Duplicated order.").
  O SDK converte em `PagBrasilRequestException`. No PagStream, o erro vem no
  envelope `{"error":{code,…}}`, e o `errorCode` fica exposto na exceção.
- **Produção usa 4xx com corpo válido.** Pedido inexistente volta HTTP 412 com
  `<request></request>` (a doc diz 200). O SDK deixa corpo XML/JSON seguir pro
  endpoint (`consultar()` devolve `null`); 412 em texto (`Invalid access.`) é
  credencial recusada.
- **Sem retry em escrita.** Sem resposta do `order/add`, consulte antes de
  reenviar (a doc alerta para cobrança duplicada). Só as leituras repetem.
- **DTOs de request** (`PagBrasil\DTO\Request\*`) normalizam na entrada:
  CPF/CNPJ só `[A-Z0-9]` (CNPJ alfanumérico incluso), CEP só dígitos, UF
  maiúscula, dinheiro como string com 2 casas (sem passar por float), datas
  `Y-m-d`. Também barram combinações que a API recusaria (ex.: duas fontes de
  cartão, parcelas fora de 1–12, filtro de assinatura sem cliente). O número
  do cartão e os tokens de cobrança não aparecem em `var_dump`/log.
- **Ambientes:** produção `https://connect.pagbrasil.com`, sandbox
  `https://sandbox.pagbrasil.com` (sobrescrevíveis por `PAGBRASIL_BASE_URL[_SANDBOX]`).
  Quem escolhe é a integração (`isSandbox()`).
- **Relatório de liquidação:** mistura `0,00`/`0.00` e datas MM/DD com DD/MM.
  As colunas de taxa não fecham o total em estorno e chargeback.

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
  PagBrasil/               Support (Signature HMAC, ParsedResponse XML/JSON/texto),
                           Bases/FormMethods (API clássica) + RestMethods (PagStream v2),
                           Endpoints/*, Webhooks/WebhookVerifier, Reconciliation/SettlementReport
  Cnab/                    CnabType, Layout, Remessa, Retorno, DTO
```

## Licença

MIT.
