# Metodos de AutomaticPix

Assinaturas implementadas em `src/Gateways/Asaas/Methods/AutomaticPix.php`.

## Indice

- [createAuthorization](#createauthorization)
- [listAuthorization](#listauthorization)
- [retrieveSingleAuthorization](#retrievesingleauthorization)
- [cancelAuthorization](#cancelauthorization)
- [retrieveSinglePaymentInstruction](#retrievesinglepaymentinstruction)
- [listPaymentInstruction](#listpaymentinstruction)

## createAuthorization

```php
Asaas::make()->automaticPix($integration)->createAuthorization(
    CreateAuthorizationRequestDTO $data
): CreateAuthorizationResponseDTO
```

## listAuthorization

```php
Asaas::make()->automaticPix($integration)->listAuthorization(
    ListAuthorizationRequestDTO $queryParams
): ListAuthorizationResponseDTO
```

## retrieveSingleAuthorization

```php
Asaas::make()->automaticPix($integration)->retrieveSingleAuthorization(
    string $id
): CreateAuthorizationResponseDTO
```

## cancelAuthorization

```php
Asaas::make()->automaticPix($integration)->cancelAuthorization(string $id): CreateAuthorizationResponseDTO
```

## retrieveSinglePaymentInstruction

```php
Asaas::make()->automaticPix($integration)->retrieveSinglePaymentInstruction(
    string $id
): SinglePaymentResponseDTO
```

## listPaymentInstruction

```php
Asaas::make()->automaticPix($integration)->listPaymentInstruction(
    ListAuthorizationPaymentsRequestDTO $queryParams
): ListAuthorizationPaymentResponseDTO
```

## Referencia

- [Documentacao Oficial - Pix Automatico](https://docs.asaas.com/docs/pix-automatico)

## Como montar os DTOs

### CreateAuthorizationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix\CreateAuthorizationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\ImmediateQrCode;
use SistemAtc\Banks\Gateways\Asaas\Enums\Frequency;

$dto = CreateAuthorizationRequestDTO::fromArray([
    'frequency' => Frequency::MONTHLY,
    'contractId' => 'contract_123',
    'startDate' => '2026-02-01',
    'customerId' => 'cus_123',
    'immediateQrCode' => ImmediateQrCode::fromArray([
        'expirationSeconds' => 600,
        'originalValue' => 120.50,
        'description' => 'Assinatura mensal',
    ]),
]);
```

### ListAuthorizationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix\ListAuthorizationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPix;

$dto = ListAuthorizationRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
    'status' => StatusPix::ACTIVE,
]);
```

### ListAuthorizationPaymentsRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix\ListAuthorizationPaymentsRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPixPayment;

$dto = ListAuthorizationPaymentsRequestDTO::fromArray([
    'authorizationId' => 'aut_123',
    'customerId' => 'cus_123',
    'status' => StatusPixPayment::SCHEDULED,
]);
```
