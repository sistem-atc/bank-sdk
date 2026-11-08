# Metodos de PaymentWithSummaryData

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PaymentWithSummaryData.php`.

## Indice

- [createNewPaymentWithSummaryDataResponse](#createnewpaymentwithsummarydataresponse)
- [listPaymentsWithSummaryData](#listpaymentswithsummarydata)
- [createNewPaymentWithCreditCardWithSummaryDataInResponse](#createnewpaymentwithcreditcardwithsummarydatainresponse)
- [capturePaymentWithPreAuthorizationWithSummaryDataInResponse](#capturepaymentwithpreauthorizationwithsummarydatainresponse)
- [retrieveSinglePaymentWithSummaryData](#retrievesinglepaymentwithsummarydata)
- [updateExistingPaymentWithSummaryDataInResponse](#updateexistingpaymentwithsummarydatainresponse)
- [deletePaymentWithSummaryData](#deletepaymentwithsummarydata)
- [restoreRemovedPaymentWithSummaryDataInResponse](#restoreremovedpaymentwithsummarydatainresponse)
- [refundPaymentWithSummaryDataInResponse](#refundpaymentwithsummarydatainresponse)
- [confirmCashReceiptWithSummaryDataInResponse](#confirmcashreceiptwithsummarydatainresponse)
- [undoCashReceiptConfirmationWithSummaryDataInResponse](#undocashreceiptconfirmationwithsummarydatainresponse)

## createNewPaymentWithSummaryDataResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->createNewPaymentWithSummaryDataResponse(
    CreatePaymentRequestDTO $data
): PaymentWithSummaryResponseDTO
```

## listPaymentsWithSummaryData

```php
Asaas::make()->paymentWithSummaryData($integration)->listPaymentsWithSummaryData(
    ListPaymentRequestDTO $data
): ListPaymentWithSummaryResponseDTO
```

## createNewPaymentWithCreditCardWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->createNewPaymentWithCreditCardWithSummaryDataInResponse(
    CreditCardPaymentRequestDTO $data
): PaymentWithSummaryCreditCardResponseDTO
```

## capturePaymentWithPreAuthorizationWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->capturePaymentWithPreAuthorizationWithSummaryDataInResponse(
    string $id
): PaymentWithSummaryResponseDTO
```

## retrieveSinglePaymentWithSummaryData

```php
Asaas::make()->paymentWithSummaryData($integration)->retrieveSinglePaymentWithSummaryData(
    string $id
): PaymentWithSummaryResponseDTO
```

## updateExistingPaymentWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->updateExistingPaymentWithSummaryDataInResponse(
    string $id
): PaymentWithSummaryResponseDTO
```

## deletePaymentWithSummaryData

```php
Asaas::make()->paymentWithSummaryData($integration)->deletePaymentWithSummaryData(
    string $id
): DeletePaymentSummaryResponseDTO
```

## restoreRemovedPaymentWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->restoreRemovedPaymentWithSummaryDataInResponse(
    string $id
): PaymentWithSummaryResponseDTO
```

## refundPaymentWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->refundPaymentWithSummaryDataInResponse(
    string $id,
    RefundPaymentRequestDTO $data
): PaymentWithSummaryResponseDTO
```

## confirmCashReceiptWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->confirmCashReceiptWithSummaryDataInResponse(
    string $id,
    ConfirmCashRequestDTO $data
): PaymentWithSummaryResponseDTO
```

## undoCashReceiptConfirmationWithSummaryDataInResponse

```php
Asaas::make()->paymentWithSummaryData($integration)->undoCashReceiptConfirmationWithSummaryDataInResponse(
    string $id
): PaymentWithSummaryResponseDTO
```

## Como montar os DTOs

### CreatePaymentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\CreatePaymentRequestDTO;
```

### ListPaymentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\ListPaymentRequestDTO;
```

### CreditCardPaymentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\CreditCardPaymentRequestDTO;
```

### RefundPaymentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\RefundPaymentRequestDTO;
```

### ConfirmCashRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment\ConfirmCashRequestDTO;
```
