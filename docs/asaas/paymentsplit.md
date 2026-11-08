# Metodos de PaymentSplit

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PaymentSplit.php`.

## Indice

- [retrieveSinglePaidSplit](#retrievesinglepaidsplit)
- [listPaidSplits](#listpaidsplits)
- [retrieveSingleReceivedSplit](#retrievesinglereceivedsplit)
- [listReceivedSplits](#listreceivedsplits)

## retrieveSinglePaidSplit

```php
Asaas::make()->paymentSplit($integration)->retrieveSinglePaidSplit(string $id): Split
```

## listPaidSplits

```php
Asaas::make()->paymentSplit($integration)->listPaidSplits(
    ListSplitsRequestDTO $data
): ListSplitResponseDTO
```

## retrieveSingleReceivedSplit

```php
Asaas::make()->paymentSplit($integration)->retrieveSingleReceivedSplit(string $id): Split
```

## listReceivedSplits

```php
Asaas::make()->paymentSplit($integration)->listReceivedSplits(
    ListSplitsRequestDTO $data
): ListSplitResponseDTO
```

## Como montar os DTOs

### ListSplitsRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentSplit\ListSplitsRequestDTO;
```
