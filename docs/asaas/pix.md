# Metodos de Pix

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Pix.php`.

## Indice

- [createKey](#createkey)
- [listKeys](#listkeys)
- [retrieveSingleKey](#retrievesinglekey)
- [removeKey](#removekey)
- [createQrCodeStatic](#createqrcodestatic)
- [removeStaticQRCode](#removestaticqrcode)
- [availableTokenBucketCheck](#availabletokenbucketcheck)

## createKey

```php
Asaas::make()->pix($integration)->createKey(CreatePixAddressKeyRequestDTO $data): PixAddressKeyResponseDTO
```

## listKeys

```php
Asaas::make()->pix($integration)->listKeys(ListKeysRequestDTO $queryParams): ListKeysResponseDTO
```

## retrieveSingleKey

```php
Asaas::make()->pix($integration)->retrieveSingleKey(string $id): PixAddressKeyResponseDTO
```

## removeKey

```php
Asaas::make()->pix($integration)->removeKey(string $id): PixAddressKeyResponseDTO
```

## createQrCodeStatic

```php
Asaas::make()->pix($integration)->createQrCodeStatic(
    CreateQRCodeStaticRequestDTO $data
): QRCodeStaticResponseDTO
```

## removeStaticQRCode

```php
Asaas::make()->pix($integration)->removeStaticQRCode(string $id): DeleteQrCodeStaticResponseDTO
```

## availableTokenBucketCheck

```php
Asaas::make()->pix($integration)->availableTokenBucketCheck(): AvailableTokenBucketCheckResponseDTO
```

## Referencia

- [Documentacao Oficial - Pix](https://docs.asaas.com/docs/pix)

## Como montar os DTOs

### CreatePixAddressKeyRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix\CreatePixAddressKeyRequestDTO;

$dto = CreatePixAddressKeyRequestDTO::fromArray([
    // Ex.: 'type' => 'EMAIL',
    // Ex.: 'value' => 'financeiro@empresa.com',
]);
```

### ListKeysRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix\ListKeysRequestDTO;

$dto = ListKeysRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```

### CreateQRCodeStaticRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix\CreateQRCodeStaticRequestDTO;

$dto = CreateQRCodeStaticRequestDTO::fromArray([
    'addressKey' => 'financeiro@empresa.com',
    'value' => 100.00,
    'description' => 'Pagamento',
]);
```
