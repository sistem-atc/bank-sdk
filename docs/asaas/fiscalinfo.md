# Metodos de FiscalInfo

Assinaturas implementadas em `src/Gateways/Asaas/Methods/FiscalInfo.php`.

## Indice

- [listMunicipalConfigurations](#listmunicipalconfigurations)
- [createAndUpdateTaxInformation](#createandupdatetaxinformation)
- [retrieveTaxInformation](#retrievetaxinformation)
- [listMunicipalServices](#listmunicipalservices)
- [listNBSCodes](#listnbscodes)
- [configureInvoiceIssuingPortal](#configureinvoiceissuingportal)
- [listFederalServiceCodes](#listfederalservicecodes)
- [listOperationIndicatorCodes](#listoperationindicatorcodes)
- [listTaxClassificationCodes](#listtaxclassificationcodes)
- [listTaxSituationCodes](#listtaxsituationcodes)

## listMunicipalConfigurations

```php
Asaas::make()->fiscalInfo($integration)->listMunicipalConfigurations(): ListMunicipalConfigurationResponseDTO
```

## createAndUpdateTaxInformation

```php
Asaas::make()->fiscalInfo($integration)->createAndUpdateTaxInformation(
    FiscalInfoRequestDTO $multipartData
): TaxInformationResponseDTO
```

## retrieveTaxInformation

```php
Asaas::make()->fiscalInfo($integration)->retrieveTaxInformation(): TaxInformationResponseDTO
```

## listMunicipalServices

```php
Asaas::make()->fiscalInfo($integration)->listMunicipalServices(
    ListMunicipalServiceRequestDTO $queryParams
): ListMunicipalServicesResponseDTO
```

## listNBSCodes

```php
Asaas::make()->fiscalInfo($integration)->listNBSCodes(
    ListNbsCodesRequestDTO $queryParams
): ListNbsCodesResponseDTO
```

## configureInvoiceIssuingPortal

```php
Asaas::make()->fiscalInfo($integration)->configureInvoiceIssuingPortal(
    ConfgureInvoiceRequestDTO $data
): ConfigureInvoiceResponseDTO
```

## listFederalServiceCodes

```php
Asaas::make()->fiscalInfo($integration)->listFederalServiceCodes(
    ListCodesRequestDTO $queryParams
): ListCodesResponseDTO
```

## listOperationIndicatorCodes

```php
Asaas::make()->fiscalInfo($integration)->listOperationIndicatorCodes(
    ListCodesRequestDTO $queryParams
): ListCodesResponseDTO
```

## listTaxClassificationCodes

```php
Asaas::make()->fiscalInfo($integration)->listTaxClassificationCodes(
    ListTaxClassificationRequestDTO $queryParams
): ListTaxClassificationResponseDTO
```

## listTaxSituationCodes

```php
Asaas::make()->fiscalInfo($integration)->listTaxSituationCodes(
    ListCodesRequestDTO $queryParams
): ListTaxSituationResponseDTO
```


## Como montar os DTOs

### FiscalInfoRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo\FiscalInfoRequestDTO;

$dto = FiscalInfoRequestDTO::fromArray([
    // Campos fiscais e arquivos (quando aplicavel)
]);
```

### ListMunicipalServiceRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo\ListMunicipalServiceRequestDTO;

$dto = ListMunicipalServiceRequestDTO::fromArray([
    // Ex.: 'municipalityCode' => '3550308',
]);
```

### ListNbsCodesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo\ListNbsCodesRequestDTO;

$dto = ListNbsCodesRequestDTO::fromArray([
    // Ex.: 'description' => 'servico',
]);
```

### ConfgureInvoiceRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo\ConfgureInvoiceRequestDTO;

$dto = ConfgureInvoiceRequestDTO::fromArray([
    // Campos de configuracao do portal
]);
```

### ListCodesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo\ListCodesRequestDTO;

$dto = ListCodesRequestDTO::fromArray([
    // Ex.: 'description' => 'consultoria',
]);
```

### ListTaxClassificationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo\ListTaxClassificationRequestDTO;

$dto = ListTaxClassificationRequestDTO::fromArray([
    // Ex.: 'description' => 'servico',
]);
```
