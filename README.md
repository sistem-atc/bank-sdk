# sistem-atc/bank-sdk

SDK PHP unificado de integração bancária — **Bradesco**, **Itaú** e **Inter PJ** — e dos
gateways de pagamento **Asaas** e **PagBrasil**. Segue o
mesmo molde do [`sistem-atc/marketplace-sdk`](https://github.com/sistem-atc/marketplace-sdk):
enums de identificação, connectors por banco/gateway, grupos de métodos por domínio, DTOs tipados e credenciais
fornecidas pelo host via contract.

Cobre Open Banking (OAuth2 client_credentials + mTLS), consulta de extrato,
cobrança (boleto e Pix), recebimentos Pix (arranjo regulatório Bacen),
pagamentos (SISPAG) e CNAB 240/400. Da PagBrasil, cobre a API inteira:
pedidos (cartão, Débito Flash, boleto, Pix), Link de Pagamento, Pix
Automático, Payout, assinaturas PagStream, IPN/webhooks e o relatório de
liquidação. Do Asaas, cobra a API v3 inteira (32 grupos de métodos, vindos do
`sistem_atc/asaas-laravel`).

## Instalação

```bash
composer require sistem-atc/bank-sdk
```

Laravel 10–13. O `BanksServiceProvider` é auto-descoberto e publica o config:

```bash
php artisan vendor:publish --tag=banks-config
```

## Uso

Cada banco é uma classe (`Banks\{Banco}\{Banco}`) que implementa o contrato
comum `BankConnector`. A regra de cada domínio é da classe do banco:

```php
use SistemAtc\Banks\Banks\Itau\Itau;

// Autenticação (client_credentials + mTLS) — devolve o AuthToken vigente
$token = Itau::make()->auth($integration);

// Extrato (conciliação)
$eventos = Itau::make()->statement($integration)->periodo('2026-07-01', '2026-07-31');

// Pagamento Pix de saída (SISPAG)
$res = Itau::make()->pix($integration)->pagar([
    'valor_pagamento' => '1260.00',
    'data_pagamento'  => '2026-07-22',
    'chave'           => 'maria_pix@gmail.com',
]);
```

Quando o banco vem de dado (a conexão guarda `'itau'`, `'bradesco'`…), o enum
`Bank` resolve o connector. O enum só identifica o banco: não tem regra.

```php
use SistemAtc\Banks\Bank;

$connector = Bank::from($conexao->banco)->connector();   // BankConnector
$connector->statement($integration)->periodo('2026-07-01', '2026-07-31');
Bank::Itau->code();                                       // '341'
```

Trocar de banco não muda o código do consumidor nos domínios comuns: cada
método é tipado pela interface de domínio, não pelo banco.

### Produtos específicos de cada banco

O contrato comum (`auth`, `statement`, `pix`, `payments`, `dda`, `code`) é o
mínimo que todo banco tem. Produtos que só um banco oferece ficam só no
connector daquele banco, tipados — o IDE/PHPStan só mostra o que o banco tem:

```php
use SistemAtc\Banks\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Banks\Inter\Inter;
use SistemAtc\Banks\Banks\Itau\Itau;

Itau::make()->boletos($integration)->emissao()->emitir([...]);
Itau::make()->recebimentosPix($integration)->cobImediata()->criar([...]);
Bradesco::make()->cobranca($integration)->registrar([...]);
Bradesco::make()->debitoVeicular($integration)->sp();
Inter::make()->cobranca($integration)->boletos()->emitir([...]);
Inter::make()->recebimentosPix($integration)->cob()->criar([...]);
```

O enum é *backed* (`'bradesco'`, `'inter'`, `'itau'`): dá pra guardar o banco
da conexão no banco de dados e resolver com `Bank::from($valor)`. Gateways de
pagamento ficam no enum `Gateway` (ver [PagBrasil](#pagbrasil)).

### Migrando da 0.x para a 2.0

| 0.x | 2.0 |
|---|---|
| `Bank::Itau->statement($i)` (e `auth`, `pix`, `payments`, `dda`) | `Itau::make()->statement($i)` ou `Bank::Itau->connector()->statement($i)` |
| `Bank::Itau->boletos($i)` e demais atalhos de produto | `Itau::make()->boletos($i)` |
| `Bank::PagBrasil`, `Bank::PagBrasil->pedidos($i)` | `Gateway::PagBrasil`, `PagBrasil::make()->pedidos($i)` |
| `SistemAtc\Banks\Itau\…`, `\Bradesco\…` | `SistemAtc\Banks\Banks\Itau\…`, `\Banks\Bradesco\…` |
| `SistemAtc\Banks\PagBrasil\…` | `SistemAtc\Banks\Gateways\PagBrasil\…` |

`Bank::X->code()`, `Bank::from()` e `Bank::X->connector()` não mudaram.

## Credenciais — o contract `BankIntegration`

O SDK **não** guarda credenciais. O host (ex.: o ERP) implementa
`SistemAtc\Banks\Contracts\BankIntegration`, entregando por request:

- `client_id` / `client_secret` do app no portal do banco;
- o **certificado mTLS** (`getCertificate(): ?ClientCertificate`) — PEM
  `.crt`+`.key` (Itaú, "certificado dinâmico"; Inter, baixado no Internet
  Banking) ou PKCS#12 `.pfx` (Bradesco);
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
- **Inter PJ** — `client_credentials` sobre mTLS (sandbox também exige o
  certificado), em `{host}/oauth/v2/token`. O token vale 1h e o endpoint de
  token aceita só **5 chamadas por minuto**, então o SDK pede um token com
  todos os escopos da integração (`settings['scopes']`, ou a lista de
  `banks.inter.scopes`) e o reaproveita em cache. Os escopos estão no enum
  `Banks\Inter\Enums\Escopo` (`Escopo::options()` serve direto num select de
  múltipla escolha); `settings['scopes']` aceita lista de `Escopo`, lista de
  strings ou string separada por espaço. O Inter recusa escopo que não foi
  marcado no app: informe só os que a aplicação da empresa tem.
  `settings['conta_corrente']` vira o header `x-conta-corrente`, obrigatório
  quando o app está ligado a mais de uma conta.
  Cada ambiente tem o seu par `.crt`+`.key`, e o sandbox do Inter só fica no
  ar de segunda a sexta, das 8h às 20h.

## Domínios

| Domínio | Itaú | Bradesco | Inter |
|---|---|---|---|
| Auth (OAuth2 + mTLS) | ✅ | ✅ | ✅ |
| Extrato / Saldo | ✅ | ✅ | ✅ |
| Pagamentos / Pix saída | ✅ | ✅ | ✅ (boleto, DARF, lote, Pix) |
| Boletos cobrança | ✅ | ✅ | ✅ (Cobrança v3, boleto com Pix) |
| Recebimentos Pix (Bacen COB/COBV/PIX/LOC/WEBHOOK + resolver QR) | ✅ | ✅ | ✅ (sem resolver QR) |
| Conciliação Pix (lançamentos conciliados) | ✅ | — | — |
| Pix Automático (recorrência + cobrança + QR) | ✅ | — | — (fora por ora) |
| Bolecode Pix | ✅ | — | ✅ (a Cobrança v3 já emite com Pix) |
| Boletos Negociados / Ativos Financeiros (recebíveis) | ✅ | — | — |
| Saque/Troco Pix | ✅ | — | — |
| Cobrança QR Code (boleto híbrido) | — | ✅ | — |
| Débito veicular (SP/MG/PR/BA) | — | ✅ | — |
| Arrecadação (contas de consumo e tributos) | — | ✅ | — |
| TED | — | ✅ | — |
| Ágora Investimentos (somente leitura) | — | ✅ | — |
| Gateway PagBrasil (ver seção própria) | — | — | — |
| CNAB 240/400 (remessa/retorno) | compartilhado | compartilhado | compartilhado |

> As APIs do Itaú vivem em hosts distintos por produto
> (`api.itau.com.br`, `pix-pj.api.itau.com`, `account-statement.api.itau.com`…);
> o SDK resolve o host por produto e ambiente.
>
> O Inter usa um host só por ambiente (`cdpj.partners.bancointer.com.br` e
> `cdpj-sandbox.partners.uatinter.co`), com o produto no path (`/banking/v2`,
> `/cobranca/v3`, `/pix/v2`). Os DTOs do Inter (`Banks/Inter/DTO/Response/*`) foram
> gerados das specs OpenAPI oficiais do portal developers.inter.co.
>
> O Inter não tem API de DDA: `Inter::make()->dda()` lança `BadMethodCallException`.

### Inter PJ

```php
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhookBanking;
use SistemAtc\Banks\Banks\Inter\Inter;

// Contrato comum
Inter::make()->statement($i)->periodo('2026-11-01', '2026-11-07');
Inter::make()->pix($i)->pagar(['chave' => '...', 'valor' => '99.90', 'identificador' => 'titulo-42']);
Inter::make()->payments($i)->pagarBoleto(['linha_digitavel' => '...', 'valor' => '26.80', 'data_vencimento' => '2026-11-10']);

// Produtos do Inter
$banking = Inter::make()->banking($i);
$banking->extrato()->saldo();
$banking->extrato()->extratoCompleto('2026-11-01', '2026-11-07', ['tipoOperacao' => 'C']);
$banking->pagamentos()->pagarDarf([...]);
$banking->webhooks()->cadastrar(TipoWebhookBanking::PixPagamento, 'https://...');

$boletos = Inter::make()->cobranca($i)->boletos();
$codigo = $boletos->emitir([...])->codigoSolicitacao;   // emissão assíncrona
$boletos->consultar($codigo)->pix->pixCopiaECola;

$pix = Inter::make()->recebimentosPix($i);
$pix->cob()->criar([...]);
$pix->pixRecebido()->devolver($e2eId, 'dev-1', ['valor' => '5.00']);
```

- **Idempotência do Pix de saída:** o `identificador` vira o header
  `x-id-idempotente`. Se não for UUID, o SDK deriva um UUID v5 estável dele,
  então o retry do mesmo título não paga duas vezes.
- **Pagamento de boleto não tem idempotência no Inter.** Antes de repetir um
  pagamento que deu timeout, consulte com `payments()->listar()`.

## Webhooks

O SDK publica sozinho a rota que recebe os avisos dos bancos e cadastra essa
mesma URL no banco — como o `marketplace-sdk` faz com os marketplaces.

```
POST {prefix}/{banco}/{integração}/{tipo}
https://erp.exemplo.com.br/api/webhooks/banks/inter/42/cobranca
```

**Cadastro** (Inter): `TipoWebhook` reúne os avisos das três APIs.

```php
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhook;

$webhooks = Inter::make()->webhooks($i);
$webhooks->cadastrar(TipoWebhook::Cobranca);                       // cobrança paga/cancelada/expirada
$webhooks->cadastrar(TipoWebhook::PixRecebido, chavePix: 'fin@x.com'); // um por chave Pix
$webhooks->consultar(TipoWebhook::PixPagamento);                   // URL cadastrada ou null
$webhooks->sincronizar(desejados: ['cobranca', 'boleto-pagamento'], anteriores: ['pix-pagamento']);
```

**Recebimento**: o aviso chega ao host pelo evento
`SistemAtc\Banks\Webhooks\BankWebhookReceived` (banco, integração, tipo,
payload, cabeçalhos como `x-conta-corrente`, corpo bruto). O host registra um
listener **síncrono** que grava o payload: se ele lançar exceção a rota
responde 500 e o banco reenvia (o Inter tenta 4 vezes); sem listener a rota
responde 503, nunca 200.

**Autenticação do Inter**: o Inter chama a URL com certificado de cliente
assinado pela CA dele. Quem confere é o servidor web que termina o TLS, e ele
repassa o resultado no cabeçalho `X-SSL-Client-Verify`. Sem `SUCCESS` a rota
responde 401.

A CA não vem no pacote: baixe o `ca.crt` do "Certificado Webhook" no
Internet Banking PJ e passe o caminho ao comando. Ela é a mesma para todo
correntista: um servidor configurado atende todas as contas Inter, e
integração nova não pede nada no nginx. O comando confere o arquivo (PEM
legível, vencimento de cada certificado — recusa vencido, avisa a menos de 90
dias) e imprime o passo a passo de uma instalação do zero:

```bash
php artisan banks:webhooks-nginx inter --ca-path=/caminho/ca.crt --upstream=http://127.0.0.1:8081

# instalar a CA (nginx local ou em outra máquina):
sudo install -D -m 644 /caminho/ca.crt /etc/nginx/certs/inter-webhook-ca.crt
ssh proxy 'sudo tee /etc/nginx/certs/inter-webhook-ca.crt >/dev/null' < /caminho/ca.crt
```

O trecho que ele gera, no `server { listen 443 ssl; }` do domínio:

```nginx
ssl_client_certificate /etc/nginx/certs/inter-webhook-ca.crt;
ssl_verify_client optional;
ssl_verify_depth 3;

location /api/webhooks/banks/inter/ {
    if ($ssl_client_verify != SUCCESS) { return 403; }
    proxy_set_header X-SSL-Client-Verify $ssl_client_verify;
    proxy_pass http://app;
}
location / {
    proxy_set_header X-SSL-Client-Verify "";   # cliente não forja o cabeçalho
    proxy_pass http://app;
}
```

Quando o Inter trocar a CA (a intermediária atual vence em 08/02/2029), baixe
o arquivo novo, confira com o comando e reinstale no servidor (recarregando o
nginx).

| Config (`banks.webhooks`) | Env | Padrão |
|---|---|---|
| `routes` | `BANKS_WEBHOOK_ROUTES` | `inter,asaas` |
| `prefix` | `BANKS_WEBHOOK_PREFIX` | `api/webhooks/banks` |
| `base_url` | `BANKS_WEBHOOK_BASE_URL` | `app.url` |
| `inter.client_verify_header` | `BANKS_WEBHOOK_INTER_CLIENT_VERIFY_HEADER` | `X-SSL-Client-Verify` (vazio desliga) |

**Asaas**: o webhook é cadastrado no painel do Asaas (Integrações →
Webhooks) com a URL `{prefix}/asaas/{integração}/eventos`
(`WebhookUrl::for('asaas', $i, AsaasWebhookReceiver::TIPO)`) e um token de
autenticação. O Asaas manda todos os eventos nessa URL com o token no
cabeçalho `asaas-access-token`; o SDK compara com o `getWebhookToken()` da
integração. Para isso o host registra um `WebhookIntegrationResolver`, que
devolve a integração pelo id da URL — sem ele a rota responde 503. O `tipo`
do evento entregue ao host é o evento do payload (`PAYMENT_RECEIVED`…); o DTO
tipado sai de `AsaasWebhookPayload::map($evento->payload)`.

```php
$this->app->bind(WebhookIntegrationResolver::class, fn () => new class implements WebhookIntegrationResolver {
    public function resolve(string $bank, string $integrationId): ?BankIntegration
    {
        return BankIntegration::query()->find($integrationId); // do banco certo e ativa
    }
});
```

Itaú e Bradesco entram no mesmo mecanismo com um `WebhookReceiver` próprio
(registrado em `WebhookReceivers`) quando o recebimento deles for implementado;
banco que conferir origem por certificado implementa também
`RequiresClientCertificate` e ganha o `banks:webhooks-nginx` de graça. A PagBrasil continua com o
`WebhookVerifier` (a URL dela já está cadastrada no host).

## Asaas

Gateway de cobrança (boleto, Pix, cartão, assinaturas, parcelamentos, split,
subcontas, transferências, NFS-e, negativação). Veio do pacote
`sistem_atc/asaas-laravel` 2.0.2 com os mesmos Methods e DTOs; mudou a
entrada e a credencial:

| asaas-laravel | bank-sdk |
|---|---|
| `Asaas::payment()` (facade, config global) | `Asaas::make()->payment($integration)` |
| `SistemAtc\Asaas\…` | `SistemAtc\Banks\Gateways\Asaas\…` (`Enum` → `Enums`) |
| `ASAAS_ACCESS_TOKEN` / `ASAAS_ENVIRONMENT` | `AsaasIntegration::getApiKey()` / `isSandbox()` |
| rota `api/asaas-events` + job + handlers + eventos por tipo | rota do SDK + `BankWebhookReceived` + `AsaasWebhookPayload::map()` |
| idempotência em cache | do host (INSERT-first com chave única no payload) |

```php
use SistemAtc\Banks\Gateways\Asaas\Asaas;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\CreatePaymentRequestDTO;

$cobranca = Asaas::make()->payment($integration)->createNewPayment(CreatePaymentRequestDTO::fromArray([
    'customer' => 'cus_G7Dvo4iphUNk',
    'billingType' => 'PIX',
    'value' => 129.90,
    'dueDate' => '2026-11-30',
]));

Asaas::make()->financialTransaction($integration);  // extrato
Asaas::make()->transfer($integration);              // TED / Pix de saída
```

A integração implementa `SistemAtc\Banks\Contracts\AsaasIntegration`:
`getApiKey()` (chave de API da conta, cabeçalho `access_token`) e
`getWebhookToken()` (token do webhook cadastrado no painel). Hosts:
`banks.asaas.base_url.production` (`https://api.asaas.com/v3`) e `.sandbox`
(`https://api-sandbox.asaas.com/v3`). A assinatura de cada método está em
[`docs/asaas/`](docs/asaas).

## PagBrasil

A PagBrasil é gateway, não banco: implementa `Contracts\GatewayConnector`
(só `make()` e `auth()`), não `BankConnector`, e não tem extrato, DDA, SISPAG
nem código FEBRABAN. O enum `Gateway` resolve o connector a partir do valor
persistido (`Gateway::from('pagbrasil')->connector()`). A integração implementa `Contracts\PagBrasilIntegration`, que
soma ao `BankIntegration` o **pbtoken**, a **secret phrase** e a **signature
key** (HMAC) do Dashboard.

```php
use SistemAtc\Banks\Gateways\PagBrasil\PagBrasil;

// Pedidos — /api/order/* (cartão, débito, boleto, Pix). Entrada tipada
// (DTO\Request\*) ou, por compatibilidade, array com os nomes da doc.
$pedido = PagBrasil::make()->pedidos($i)->pix(new PixOrder(
    order: 'PED-123', productName: 'Whey 900g', products: [new Product('WHEY-900', 129.9)],
    customer: new Customer('José da Silva', '529.982.247-25', 'jose@x.com', '11999990000'),
    address: new Address('Av. Paulista, 100', '01311-100', 'São Paulo', 'SP'),
    amountBrl: 129.9, expirationMinutes: 30,
));
$pedido->pixCode;                                   // copia-e-cola
PagBrasil::make()->pedidos($i)->consultar('PED-123'); // null se não existe
PagBrasil::make()->pedidos($i)->estornar(new Refund('PED-123', '129.90', new RefundPixKey(PixKeyType::Email, 'jose@x.com')));

// Link de Pagamento, Pix Automático, Payout
PagBrasil::make()->linkPagamento($i)->criar(['order' => 'PED-124', 'amount_brl' => '50.00'])->urlPayment;
PagBrasil::make()->pixAutomatico($i)->consultarRecorrencia($pixRecId);
PagBrasil::make()->payout($i)->enviar('91051605962', '150.00', 'Comissão');

// PagStream (assinaturas) — API REST v2 + as rotas que ainda são v1
PagBrasil::make()->pagStream($i)->assinaturas()->listar(['tax_id' => '12345678909']);
PagBrasil::make()->pagStream($i)->recorrencias()->pular('P17648804286', 'next');

// IPN / webhook — confere secret + HMAC e devolve a notificação tipada
$notificacao = PagBrasil::make()->webhook($i)->parse($request->all());
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
| OAuth (PagBrasil.JS) | `/api/oauth/token` — via `PagBrasil::make()->auth($i)` |

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
  Bank.php                 enum dos bancos → BankConnector
  Gateway.php              enum dos gateways → GatewayConnector
  Contracts/               BankIntegration (+ PagBrasil/AsaasIntegration), BankConnector, GatewayConnector,
                           WebhookIntegrationResolver, DTOInterface, Endpoints/*
  Common/                  AutoHydrate, CastToArray, attributes, HttpMethod
  Support/                 AuthToken, ClientCertificate, MtlsOptions, PrivateKeyJwt
  Exceptions/              BankAuthenticationException, BankRequestException
  Banks/                   um diretório por banco:
    Itau/ Bradesco/ Inter/ Support (OAuth/HttpClientFactory/TokenRefresher),
                           Bases/BaseMethods, Endpoints/*, DTO/Response/*
  Gateways/                um diretório por gateway:
    Asaas/                 Support/HttpClientFactory (chave de API), Bases/BaseMethods, Methods/* (32),
                           DTO/Request|Response|Shared|Webhook, Enums/*, Webhooks/AsaasWebhookPayload
    PagBrasil/             Support (Signature HMAC, ParsedResponse XML/JSON/texto),
                           Bases/FormMethods (API clássica) + RestMethods (PagStream v2),
                           Endpoints/*, Webhooks/WebhookVerifier, Reconciliation/SettlementReport
  Cnab/                    CnabType, Layout, Remessa, Retorno, DTO
  Webhooks/                WebhookUrl, BankWebhookReceived (evento), Receivers/* por banco/gateway
  Http/Controllers/        BankWebhookController (rota registrada pelo ServiceProvider)
  Console/                 banks:webhooks-nginx
docs/asaas/                assinatura dos métodos de cada grupo do Asaas
```

## Licença

MIT.
