# Metodos de PaymentDocument

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PaymentDocument.php`.

## Indice

- [uploadPaymentDocuments](#uploadpaymentdocuments)
- [listDocumentsPayment](#listdocumentspayment)
- [updateSettingsaDocumentPayment](#updatesettingsadocumentpayment)
- [retrieveSingleDocumentPayment](#retrievesingledocumentpayment)
- [deleteDocumentFromPayment](#deletedocumentfrompayment)

## uploadPaymentDocuments

```php
Asaas::make()->paymentDocument($integration)->uploadPaymentDocuments(
    string $id,
    UploadPaymentDocumentRequestDTO $data
): PaymentDocumentResponseDTO
```

## listDocumentsPayment

```php
Asaas::make()->paymentDocument($integration)->listDocumentsPayment(
    string $id
): ListPaymentDocumentResponseDTO
```

## updateSettingsaDocumentPayment

```php
Asaas::make()->paymentDocument($integration)->updateSettingsaDocumentPayment(
    string $id,
    string $documentId,
    UpdateSettingsDocumentRequestDTO $data
): PaymentDocumentResponseDTO
```

## retrieveSingleDocumentPayment

```php
Asaas::make()->paymentDocument($integration)->retrieveSingleDocumentPayment(
    string $id,
    string $documentId
): PaymentDocumentResponseDTO
```

## deleteDocumentFromPayment

```php
Asaas::make()->paymentDocument($integration)->deleteDocumentFromPayment(
    string $id,
    string $documentId
): DeletePaymentDocumentResponseDTO
```

## Como montar os DTOs

### UploadPaymentDocumentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDocument\UploadPaymentDocumentRequestDTO;
```

### UpdateSettingsDocumentRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDocument\UpdateSettingsDocumentRequestDTO;
```
