# Metodos de MobilePhoneRecharge

Assinaturas implementadas em `src/Gateways/Asaas/Methods/MobilePhoneRecharge.php`.

## Indice

- [requestRecharge](#requestrecharge)
- [listCellPhoneTopups](#listcellphonetopups)
- [recoverSingleCellPhoneRecharge](#recoversinglecellphonerecharge)
- [cancelCellPhoneRecharge](#cancelcellphonerecharge)
- [searchCellPhoneProvider](#searchcellphoneprovider)

## requestRecharge

```php
Asaas::make()->mobilePhoneRecharge($integration)->requestRecharge(
    RechargeRequestDTO $data
): RechargeResponseDTO
```

## listCellPhoneTopups

```php
Asaas::make()->mobilePhoneRecharge($integration)->listCellPhoneTopups(
    ListCellPhonesRequestDTO $data
): ListCellPhonesResponseDTO
```

## recoverSingleCellPhoneRecharge

```php
Asaas::make()->mobilePhoneRecharge($integration)->recoverSingleCellPhoneRecharge(
    string $id
): RechargeResponseDTO
```

## cancelCellPhoneRecharge

```php
Asaas::make()->mobilePhoneRecharge($integration)->cancelCellPhoneRecharge(
    string $id
): RechargeResponseDTO
```

## searchCellPhoneProvider

```php
Asaas::make()->mobilePhoneRecharge($integration)->searchCellPhoneProvider(
    string $phoneNumber
): SearchCellPhoneResponseDTO
```

## Como montar os DTOs

### RechargeRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\MobilePhoneRecharge\RechargeRequestDTO;
```

### ListCellPhonesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\MobilePhoneRecharge\ListCellPhonesRequestDTO;
```
