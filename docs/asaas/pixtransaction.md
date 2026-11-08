# Metodos de PixTransaction

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PixTransaction.php`.

## Indice

- [payQRCode](#payqrcode)
- [decodeQRCodePayment](#decodeqrcodepayment)
- [retrieveSingleTransaction](#retrievesingletransaction)
- [listTransactions](#listtransactions)
- [cancelScheduledTransaction](#cancelscheduledtransaction)

## payQRCode

```php
Asaas::make()->pixTransaction($integration)->payQRCode(
    PayQrCodeRequestDTO $data
): PayQrCodeResponseDTO
```

## decodeQRCodePayment

```php
Asaas::make()->pixTransaction($integration)->decodeQRCodePayment(
    DecodeQrCodeRequestDTO $data
): DecodeQrCodeResponseDTO
```

## retrieveSingleTransaction

```php
Asaas::make()->pixTransaction($integration)->retrieveSingleTransaction(
    string $id
): PayQrCodeResponseDTO
```

## listTransactions

```php
Asaas::make()->pixTransaction($integration)->listTransactions(
    ListTransactionsRequestDTO $data
): ListTransactionResponseDTO
```

## cancelScheduledTransaction

```php
Asaas::make()->pixTransaction($integration)->cancelScheduledTransaction(
    string $id
): PayQrCodeResponseDTO
```

## Como montar os DTOs

### PayQrCodeRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction\PayQrCodeRequestDTO;
```

### DecodeQrCodeRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction\DecodeQrCodeRequestDTO;
```

### ListTransactionsRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction\ListTransactionsRequestDTO;
```
