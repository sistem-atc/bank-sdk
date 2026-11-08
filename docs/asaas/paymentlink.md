# Metodos de PaymentLink

Assinaturas implementadas em `src/Gateways/Asaas/Methods/PaymentLink.php`.

## Indice

- [createPaymentsLink](#createpaymentslink)
- [listPaymentsLinks](#listpaymentslinks)
- [updatePaymentsLink](#updatepaymentslink)
- [retrieveSinglePaymentsLink](#retrievesinglepaymentslink)
- [removePaymentsLink](#removepaymentslink)
- [restorePaymentsLink](#restorepaymentslink)
- [addImagePaymentsLink](#addimagepaymentslink)
- [listImagesPaymentsLink](#listimagespaymentslink)
- [retrieveSinglePaymentsLinkImage](#retrievesinglepaymentslinkimage)
- [removeImageFromPaymentsLink](#removeimagefrompaymentslink)
- [setPaymentsLinkMainImage](#setpaymentslinkmainimage)

## createPaymentsLink

```php
Asaas::make()->paymentLink($integration)->createPaymentsLink(
    PaymentLinkRequestDTO $data
): PaymentLinkResponseDTO
```

## listPaymentsLinks

```php
Asaas::make()->paymentLink($integration)->listPaymentsLinks(
    ListPaymentLinkRequestDTO $data
): ListPaymentLinkResponseDTO
```

## updatePaymentsLink

```php
Asaas::make()->paymentLink($integration)->updatePaymentsLink(
    string $id,
    PaymentLinkRequestDTO $data
): PaymentLinkResponseDTO
```

## retrieveSinglePaymentsLink

```php
Asaas::make()->paymentLink($integration)->retrieveSinglePaymentsLink(
    string $id
): PaymentLinkResponseDTO
```

## removePaymentsLink

```php
Asaas::make()->paymentLink($integration)->removePaymentsLink(string $id): DeletePaymentLinkResponseDTO
```

## restorePaymentsLink

```php
Asaas::make()->paymentLink($integration)->restorePaymentsLink(string $id): PaymentLinkResponseDTO
```

## addImagePaymentsLink

```php
Asaas::make()->paymentLink($integration)->addImagePaymentsLink(
    string $id,
    AddImagePaymentLinkRequestDTO $data
): ImagePaymentLinkResponseDTO
```

## listImagesPaymentsLink

```php
Asaas::make()->paymentLink($integration)->listImagesPaymentsLink(
    string $id
): ListImagePaymentLinkResponseDTO
```

## retrieveSinglePaymentsLinkImage

```php
Asaas::make()->paymentLink($integration)->retrieveSinglePaymentsLinkImage(
    string $paymentLinkId,
    string $imageId
): ImagePaymentLinkResponseDTO
```

## removeImageFromPaymentsLink

```php
Asaas::make()->paymentLink($integration)->removeImageFromPaymentsLink(
    string $paymentLinkId,
    string $imageId
): DeletePaymentLinkResponseDTO
```

## setPaymentsLinkMainImage

```php
Asaas::make()->paymentLink($integration)->setPaymentsLinkMainImage(
    string $paymentLinkId,
    string $imageId
): ImagePaymentLinkResponseDTO
```

## Como montar os DTOs

### PaymentLinkRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentLink\PaymentLinkRequestDTO;
```

### ListPaymentLinkRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentLink\ListPaymentLinkRequestDTO;
```

### AddImagePaymentLinkRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentLink\AddImagePaymentLinkRequestDTO;
```
