# Metodos de CreditBureauReport

Assinaturas implementadas em `src/Gateways/Asaas/Methods/CreditBureauReport.php`.

## Indice

- [makeConsultation](#makeconsultation)
- [listCreditBureauReports](#listcreditbureaureports)
- [retrieveCreditBureauReport](#retrievecreditbureaureport)

## makeConsultation

```php
Asaas::make()->creditBureauReport($integration)->makeConsultation(
    MakeConsultationRequestDTO $data
): MakeConsultationResponseDTO
```

## listCreditBureauReports

```php
Asaas::make()->creditBureauReport($integration)->listCreditBureauReports(
    ListCreditBureauReportsRequestDTO $queryParams
): ListCreditBureauReportsResponseDTO
```

## retrieveCreditBureauReport

```php
Asaas::make()->creditBureauReport($integration)->retrieveCreditBureauReport(
    string $id
): MakeConsultationResponseDTO
```


## Como montar os DTOs

### MakeConsultationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditBureauReport\MakeConsultationRequestDTO;

$dto = MakeConsultationRequestDTO::fromArray([
    // Campos da consulta
]);
```

### ListCreditBureauReportsRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditBureauReport\ListCreditBureauReportsRequestDTO;

$dto = ListCreditBureauReportsRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```
