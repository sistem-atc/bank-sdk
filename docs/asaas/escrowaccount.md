# Metodos de EscrowAccount

Assinaturas implementadas em `src/Gateways/Asaas/Methods/EscrowAccount.php`.

## Indice

- [SaveOrUpdateEscrowAccount](#saveorupdateescrowaccount)
- [CreateDefaultEscrowAccount](#createdefaultescrowaccount)
- [FinishPaymentEscrow](#finishpaymentescrow)
- [retrieveEscrowAccount](#retrieveescrowaccount)
- [retrieveDefaultEscrowAccount](#retrievedefaultescrowaccount)
- [retrievePaymentEscrow](#retrievepaymentescrow)

## SaveOrUpdateEscrowAccount

```php
Asaas::make()->escrowAccount($integration)->SaveOrUpdateEscrowAccount(
    string $id,
    EscrowAccountRequestDTO $data
): EscrowAccountResponseDTO
```

## CreateDefaultEscrowAccount

```php
Asaas::make()->escrowAccount($integration)->CreateDefaultEscrowAccount(
    EscrowAccountRequestDTO $data
): EscrowAccountResponseDTO
```

## FinishPaymentEscrow

```php
Asaas::make()->escrowAccount($integration)->FinishPaymentEscrow(
    string $id
): FinishPaymentEscrowResponseDTO
```

## retrieveEscrowAccount

```php
Asaas::make()->escrowAccount($integration)->retrieveEscrowAccount(
    string $id
): EscrowAccountResponseDTO
```

## retrieveDefaultEscrowAccount

```php
Asaas::make()->escrowAccount($integration)->retrieveDefaultEscrowAccount(): EscrowAccountResponseDTO
```

## retrievePaymentEscrow

```php
Asaas::make()->escrowAccount($integration)->retrievePaymentEscrow(string $id): EscrowResponseDTO
```


## Como montar os DTOs

### EscrowAccountRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\EscrowAccount\EscrowAccountRequestDTO;

$dto = EscrowAccountRequestDTO::fromArray([
    // Campos da configuracao de escrow
]);
```
