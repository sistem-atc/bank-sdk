# Metodos de Informacoes da Conta (AccountInfo)

Assinaturas implementadas em `src/Gateways/Asaas/Methods/AccountInfo.php`.

## Indice

- [retrieveBusinessData](#retrievebusinessdata)
- [updateBusinessData](#updatebusinessdata)
- [savePaymentCheckoutCustomization](#savepaymentcheckoutcustomization)
- [retrievePersonalizationSettings](#retrievepersonalizationsettings)
- [retrieveAsaasAccountNumber](#retrieveasaasaccountnumber)
- [retrieveAccountFees](#retrieveaccountfees)
- [checkAccountStatus](#checkaccountstatus)
- [retrieveWalletId](#retrievewalletid)
- [deleteWhiteLabelSubaccount](#deletewhitelabelsubaccount)

## retrieveBusinessData

```php
Asaas::make()->accountInfo($integration)->retrieveBusinessData(): RetrieveBusinessDataResponseDTO
```

## updateBusinessData

```php
Asaas::make()->accountInfo($integration)->updateBusinessData(
    UpdateBusinessDataRequestDTO $data
): RetrieveBusinessDataResponseDTO
```

## savePaymentCheckoutCustomization

```php
Asaas::make()->accountInfo($integration)->savePaymentCheckoutCustomization(
    UpdateCheckoutCustomizationRequestDTO $data
): AccountStatus
```

## retrievePersonalizationSettings

```php
Asaas::make()->accountInfo($integration)->retrievePersonalizationSettings(): AccountStatus
```

## retrieveAsaasAccountNumber

```php
Asaas::make()->accountInfo($integration)->retrieveAsaasAccountNumber(): RetrieveAsaasAccountNumberResponseDTO
```

## retrieveAccountFees

```php
Asaas::make()->accountInfo($integration)->retrieveAccountFees(): RetrieveAccountFeesResponseDTO
```

## checkAccountStatus

```php
Asaas::make()->accountInfo($integration)->checkAccountStatus(): AccountStatus
```

## retrieveWalletId

```php
Asaas::make()->accountInfo($integration)->retrieveWalletId(): RetrieveWalletIdResponseDTO
```

## deleteWhiteLabelSubaccount

```php
Asaas::make()->accountInfo($integration)->deleteWhiteLabelSubaccount(
    string $removeReason
): DeleteWhiteLabelSubaccountResponseDTO
```

## Referencia

- [Documentacao Oficial - Conta](https://docs.asaas.com/docs/conta)

## Como montar os DTOs

### UpdateBusinessDataRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo\UpdateBusinessDataRequestDTO;

$dto = UpdateBusinessDataRequestDTO::fromArray([
    // Ex.: 'email' => 'financeiro@empresa.com',
    // Ex.: 'phone' => '11999999999',
]);
```

### UpdateCheckoutCustomizationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo\UpdateCheckoutCustomizationRequestDTO;

$dto = UpdateCheckoutCustomizationRequestDTO::fromArray([
    // Ex.: 'mainColor' => '#0052CC',
    // Ex.: 'logoFile' => storage_path('app/logo.png'),
]);
```
