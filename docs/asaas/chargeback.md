# Metodos de Chargeback

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Chargeback.php`.

## Indice

- [createChargebackDispute](#createchargebackdispute)
- [listChargebacks](#listchargebacks)
- [retrieveSingleChargeback](#retrievesinglechargeback)

## createChargebackDispute

```php
Asaas::make()->chargeback($integration)->createChargebackDispute(
    string $id,
    CreateChargebackDisputeRequestDTO $data
): ChargebackDisputeResponseDTO
```

## listChargebacks

```php
Asaas::make()->chargeback($integration)->listChargebacks(
    ListChargebacksRequestDTO $queryParams
): ListChargebackResponseDTO
```

## retrieveSingleChargeback

```php
Asaas::make()->chargeback($integration)->retrieveSingleChargeback(?string $id): ChargebackResponseDTO
```


## Como montar os DTOs

### CreateChargebackDisputeRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback\CreateChargebackDisputeRequestDTO;

$dto = CreateChargebackDisputeRequestDTO::fromArray([
    // Campos da contestacao
]);
```

### ListChargebacksRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback\ListChargebacksRequestDTO;

$dto = ListChargebacksRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```
