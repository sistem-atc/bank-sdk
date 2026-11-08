# Metodos de Transfer

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Transfer.php`.

## Indice

- [transferAnotherInstitutionAccountOrPixKey](#transferanotherinstitutionaccountorpixkey)
- [listTransfers](#listtransfers)
- [transferAsaasAccount](#transferasaasaccount)
- [retrieveSingleTransfer](#retrievesingletransfer)
- [cancelTransfer](#canceltransfer)

## transferAnotherInstitutionAccountOrPixKey

```php
Asaas::make()->transfer($integration)->transferAnotherInstitutionAccountOrPixKey(
    TransferAnotherInstitutionRequestDTO $data
): TranferAnotherResponseDTO
```

## listTransfers

```php
Asaas::make()->transfer($integration)->listTransfers(
    ListTransferRequestDTO $queryParams
): ListTransferResponseDTO
```

## transferAsaasAccount

```php
Asaas::make()->transfer($integration)->transferAsaasAccount(
    TransferAsaasAccountRequestDTO $data
): TransferAsaasResponseDTO
```

## retrieveSingleTransfer

```php
Asaas::make()->transfer($integration)->retrieveSingleTransfer(string $id): TranferAnotherResponseDTO
```

## cancelTransfer

```php
Asaas::make()->transfer($integration)->cancelTransfer(string $id): TranferAnotherResponseDTO
```


## Como montar os DTOs

### TransferAnotherInstitutionRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer\TransferAnotherInstitutionRequestDTO;

$dto = TransferAnotherInstitutionRequestDTO::fromArray([
    // Campos de transferencia TED/Pix para outra instituicao
]);
```

### ListTransferRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer\ListTransferRequestDTO;

$dto = ListTransferRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```

### TransferAsaasAccountRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer\TransferAsaasAccountRequestDTO;

$dto = TransferAsaasAccountRequestDTO::fromArray([
    // Campos de transferencia entre contas Asaas
]);
```
