# Metodos de Finance

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Finance.php`.

## Indice

- [RetrieveAccountBalance](#retrieveaccountbalance)
- [collectionsStatistics](#collectionsstatistics)
- [retrieveSplitValues](#retrievesplitvalues)

## RetrieveAccountBalance

```php
Asaas::make()->finance($integration)->RetrieveAccountBalance(): BalanceResponseDTO
```

## collectionsStatistics

```php
Asaas::make()->finance($integration)->collectionsStatistics(
    CollectionStatisticsRequestDTO $queryParams
): CollectionStatisticsResponseDTO
```

## retrieveSplitValues

```php
Asaas::make()->finance($integration)->retrieveSplitValues(): CollectionStatisticsResponseDTO
```


## Como montar os DTOs

### CollectionStatisticsRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Finance\CollectionStatisticsRequestDTO;

$dto = CollectionStatisticsRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```
