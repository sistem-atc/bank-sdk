# Metodos de FinancialTransaction

Assinaturas implementadas em `src/Gateways/Asaas/Methods/FinancialTransaction.php`.

## Indice

- [retrieveExtract](#retrieveextract)

## retrieveExtract

```php
Asaas::make()->financialTransaction($integration)->retrieveExtract(
    RetrieveExtractRequestDTO $queryParams
): RetrieveExtractResponseDTO
```


## Como montar os DTOs

### RetrieveExtractRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\FinancialTransaction\RetrieveExtractRequestDTO;

$dto = RetrieveExtractRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```
