# Metodos de PaymentDunning

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PaymentDunning.php`.

## Indice

- [createPaymentDunning](#createpaymentdunning)
- [listPaymentDunnings](#listpaymentdunnings)
- [simulatePaymentDunning](#simulatepaymentdunning)
- [recoverSinglePaymentDunning](#recoversinglepaymentdunning)
- [eventHistoryLists](#eventhistorylists)
- [listPaymentsReceived](#listpaymentsreceived)
- [listPaymentsAvailablePaymentDunning](#listpaymentsavailablepaymentdunning)
- [resendDocuments](#resenddocuments)
- [cancelPaymentDunning](#cancelpaymentdunning)

## createPaymentDunning

```php
Asaas::make()->paymentDunning($integration)->createPaymentDunning(
    PaymentDunningRequestDTO $data
): PaymentDunningResponseDTO
```

## listPaymentDunnings

```php
Asaas::make()->paymentDunning($integration)->listPaymentDunnings(
    ListPaymentDunningRequestDTO $data
): ListPaymentDunningResponseDTO
```

## simulatePaymentDunning

```php
Asaas::make()->paymentDunning($integration)->simulatePaymentDunning(
    SimulatePaymentDunningRequestDTO $data
): SimulatePaymentDunningResponseDTO
```

## recoverSinglePaymentDunning

```php
Asaas::make()->paymentDunning($integration)->recoverSinglePaymentDunning(
    string $id
): PaymentDunningResponseDTO
```

## eventHistoryLists

```php
Asaas::make()->paymentDunning($integration)->eventHistoryLists(
    string $id,
    ListsDunningRequestDTO $data
): HistoryListResponseDTO
```

## listPaymentsReceived

```php
Asaas::make()->paymentDunning($integration)->listPaymentsReceived(
    string $id,
    ListsDunningRequestDTO $data
): ListPaymentReceivedResponseDTO
```

## listPaymentsAvailablePaymentDunning

```php
Asaas::make()->paymentDunning($integration)->listPaymentsAvailablePaymentDunning(
    ListsDunningRequestDTO $data
): ListPaymentsAvaliableResponseDTO
```

## resendDocuments

```php
Asaas::make()->paymentDunning($integration)->resendDocuments(
    string $id,
    ResendDocumentRequestDTO $data
): PaymentDunningResponseDTO
```

## cancelPaymentDunning

```php
Asaas::make()->paymentDunning($integration)->cancelPaymentDunning(
    string $id
): PaymentDunningResponseDTO
```

## Como montar os DTOs

### PaymentDunningRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\PaymentDunningRequestDTO;
```

### ListPaymentDunningRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\ListPaymentDunningRequestDTO;
```

### SimulatePaymentDunningRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\SimulatePaymentDunningRequestDTO;
```

### ListsDunningRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\ListsDunningRequestDTO;
```

### ResendDocumentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning\ResendDocumentRequestDTO;
```
