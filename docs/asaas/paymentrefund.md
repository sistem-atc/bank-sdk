# Metodos de PaymentRefund

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PaymentRefund.php`.

## Indice

- [retrieveRefundsSinglePayment](#retrieverefundssinglepayment)
- [refundBankSlip](#refundbankslip)

## retrieveRefundsSinglePayment

```php
Asaas::make()->paymentRefund($integration)->retrieveRefundsSinglePayment(
    string $id
): RetrieveSinglePaymentResponseDTO
```

## refundBankSlip

```php
Asaas::make()->paymentRefund($integration)->refundBankSlip(string $id): RefundBankSlipResponseDTO
```

## Como montar os DTOs

Este modulo nao recebe DTO de entrada nos metodos implementados atualmente.
As operacoes usam apenas `string $id` como parametro.
