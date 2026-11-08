# Metodos de Subaccount

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Subaccount.php`.

## Indice

- [createSubaccount](#createsubaccount)
- [listSubaccounts](#listsubaccounts)
- [retrieveSingleSubaccount](#retrievesinglesubaccount)
- [createAPIkeyForSubaccount](#createapikeyforsubaccount)
- [listAPIkeysForSubaccount](#listapikeysforsubaccount)
- [updateAPIkeyForSubaccount](#updateapikeyforsubaccount)
- [deleteAPIkeyForSubaccount](#deleteapikeyforsubaccount)

## createSubaccount

```php
Asaas::make()->subAccount($integration)->createSubaccount(
    SubAccountRequestDTO $data
): SubAccountResponseDTO
```

## listSubaccounts

```php
Asaas::make()->subAccount($integration)->listSubaccounts(
    ListSubAccountRequestDTO $data
): ListSubAccountResponseDTO
```

## retrieveSingleSubaccount

```php
Asaas::make()->subAccount($integration)->retrieveSingleSubaccount(
    string $id
): SubAccountResponseDTO
```

## createAPIkeyForSubaccount

```php
Asaas::make()->subAccount($integration)->createAPIkeyForSubaccount(
    string $id,
    ApiKeySubAccountRequestDTO $data
): ApiKeySubAccountResponseDTO
```

## listAPIkeysForSubaccount

```php
Asaas::make()->subAccount($integration)->listAPIkeysForSubaccount(
    string $id
): ListAccessTokenResponseDTO
```

## updateAPIkeyForSubaccount

```php
Asaas::make()->subAccount($integration)->updateAPIkeyForSubaccount(
    string $id,
    string $accessTokenId,
    UpdateApiKeyRequestDTO $data
): UpdateApiKeySubAccountResponseDTO
```

## deleteAPIkeyForSubaccount

```php
Asaas::make()->subAccount($integration)->deleteAPIkeyForSubaccount(
    string $id,
    string $accessTokenId
): bool
```

## Como montar os DTOs

### SubAccountRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subaccount\SubAccountRequestDTO;
```

### ListSubAccountRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subaccount\ListSubAccountRequestDTO;
```

### ApiKeySubAccountRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subaccount\ApiKeySubAccountRequestDTO;
```

### UpdateApiKeyRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subaccount\UpdateApiKeyRequestDTO;
```
