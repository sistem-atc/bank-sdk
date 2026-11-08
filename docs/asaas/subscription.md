# Metodos de Subscription (Assinaturas)

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Subscription.php`.

## Indice

- [createNewSubscription](#createnewsubscription)
- [list](#list)
- [createSubscriptionWithCreditCard](#createsubscriptionwithcreditcard)
- [retrieveSingleSubscription](#retrievesinglesubscription)
- [update](#update)
- [remove](#remove)
- [updateCreditCard](#updatecreditcard)
- [listPaymentsSubscription](#listpaymentssubscription)
- [generateSubscriptionBooklet](#generatesubscriptionbooklet)
- [createConfigurationForIssuingInvoices](#createconfigurationforissuinginvoices)
- [retrieveConfigurationForIssuingInvoices](#retrieveconfigurationforissuinginvoices)
- [removeConfigurationForIssuingInvoices](#removeconfigurationforissuinginvoices)
- [updateConfigurationForIssuingInvoices](#updateconfigurationforissuinginvoices)
- [listInvoicesForSubscriptionCharges](#listinvoicesforsubscriptioncharges)

## createNewSubscription

```php
Asaas::make()->subscription($integration)->createNewSubscription(
    CreateSubscriptionRequestDTO $data
): SubscriptionResponseDTO
```

## list

```php
Asaas::make()->subscription($integration)->list(
    ListSubscriptionRequestDTO $data
): ListSubscriptionResponseDTO
```

## createSubscriptionWithCreditCard

```php
Asaas::make()->subscription($integration)->createSubscriptionWithCreditCard(
    CreateSubscriptionCreditCardRequestDTO $data
): SubscriptionCreditCardResponseDTO
```

## retrieveSingleSubscription

```php
Asaas::make()->subscription($integration)->retrieveSingleSubscription(
    string $id
): SubscriptionResponseDTO
```

## update

```php
Asaas::make()->subscription($integration)->update(
    string $id,
    UpdateSubscriptionRequestDTO $data
): SubscriptionResponseDTO
```

## remove

```php
Asaas::make()->subscription($integration)->remove(string $id): DeleteSubscriptionResponseDTO
```

## updateCreditCard

```php
Asaas::make()->subscription($integration)->updateCreditCard(
    string $id,
    UpdateSubscriptionCreditCardRequestDTO $data
): SubscriptionResponseDTO
```

## listPaymentsSubscription

```php
Asaas::make()->subscription($integration)->listPaymentsSubscription(
    string $id,
    ListPaymentSubscriptionRequestDTO $queryParams
): ListPaymentSubscriptionResponseDTO
```

## generateSubscriptionBooklet

```php
Asaas::make()->subscription($integration)->generateSubscriptionBooklet(
    string $id,
    BookletPaymentSubscriptionRequestDTO $queryParams
): FileResponseDTO
```

## createConfigurationForIssuingInvoices

```php
Asaas::make()->subscription($integration)->createConfigurationForIssuingInvoices(
    string $id,
    ConfigurationInvoicesRequestDTO $data
): ConfigurationInvoicesResponseDTO
```

## retrieveConfigurationForIssuingInvoices

```php
Asaas::make()->subscription($integration)->retrieveConfigurationForIssuingInvoices(
    string $id
): ConfigurationInvoicesResponseDTO
```

## removeConfigurationForIssuingInvoices

```php
Asaas::make()->subscription($integration)->removeConfigurationForIssuingInvoices(
    string $id
): DeleteConfigurationResponseDTO
```

## updateConfigurationForIssuingInvoices

```php
Asaas::make()->subscription($integration)->updateConfigurationForIssuingInvoices(
    string $id,
    UpdateConfigurationInvoicesRequestDTO $data
): ConfigurationInvoicesResponseDTO
```

## listInvoicesForSubscriptionCharges

```php
Asaas::make()->subscription($integration)->listInvoicesForSubscriptionCharges(
    string $id,
    $queryParams
): ListInvoicesForSubscriptionResponseDTO
```

## Referencia

- [Documentacao Oficial - Assinaturas](https://docs.asaas.com/docs/assinaturas)

## Como montar os DTOs

### CreateSubscriptionRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\CreateSubscriptionRequestDTO;

$dto = CreateSubscriptionRequestDTO::fromArray([
    'customer' => 'cus_xxx',
    'billingType' => 'BOLETO',
    'value' => 99.90,
    'nextDueDate' => '2026-03-01',
]);
```

### ListSubscriptionRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\ListSubscriptionRequestDTO;

$dto = ListSubscriptionRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```

### CreateSubscriptionCreditCardRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\CreateSubscriptionCreditCardRequestDTO;

$dto = CreateSubscriptionCreditCardRequestDTO::fromArray([
    // Campos da assinatura com cartao
]);
```

### UpdateSubscriptionRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\UpdateSubscriptionRequestDTO;

$dto = UpdateSubscriptionRequestDTO::fromArray([
    // Campos permitidos para atualizacao
]);
```

### UpdateSubscriptionCreditCardRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\UpdateSubscriptionCreditCardRequestDTO;

$dto = UpdateSubscriptionCreditCardRequestDTO::fromArray([
    // Campos do novo cartao
]);
```

### ListPaymentSubscriptionRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\ListPaymentSubscriptionRequestDTO;

$dto = ListPaymentSubscriptionRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```

### BookletPaymentSubscriptionRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\BookletPaymentSubscriptionRequestDTO;

$dto = BookletPaymentSubscriptionRequestDTO::fromArray([
    // Ex.: filtros para gerar carnê da assinatura
]);
```

### ConfigurationInvoicesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\ConfigurationInvoicesRequestDTO;

$dto = ConfigurationInvoicesRequestDTO::fromArray([
    // Campos de configuracao de emissao de nota
]);
```

### UpdateConfigurationInvoicesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription\UpdateConfigurationInvoicesRequestDTO;

$dto = UpdateConfigurationInvoicesRequestDTO::fromArray([
    // Campos para atualizar configuracao de notas
]);
```
