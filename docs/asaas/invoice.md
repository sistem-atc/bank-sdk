# Metodos de Invoice

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Invoice.php`.

## Indice

- [scheduleInvoice](#scheduleinvoice)
- [listInvoices](#listinvoices)
- [updateInvoice](#updateinvoice)
- [retrieveSingleInvoice](#retrievesingleinvoice)
- [issueInvoice](#issueinvoice)
- [cancelInvoice](#cancelinvoice)

## scheduleInvoice

```php
Asaas::make()->invoice($integration)->scheduleInvoice(
    ScheduleInvoiceRequestDTO $data
): InvoiceResponseDTO
```

## listInvoices

```php
Asaas::make()->invoice($integration)->listInvoices(
    ListInvoicesRequestDTO $data
): ListInvoiceResponseDTO
```

## updateInvoice

```php
Asaas::make()->invoice($integration)->updateInvoice(
    string $id,
    UpdateInvoiceRequestDTO $data
): InvoiceResponseDTO
```

## retrieveSingleInvoice

```php
Asaas::make()->invoice($integration)->retrieveSingleInvoice(string $id): InvoiceResponseDTO
```

## issueInvoice

```php
Asaas::make()->invoice($integration)->issueInvoice(string $id): InvoiceResponseDTO
```

## cancelInvoice

```php
Asaas::make()->invoice($integration)->cancelInvoice(
    string $id,
    CancelInvoiceRequestDTO $data
): InvoiceResponseDTO
```

## Como montar os DTOs

### ScheduleInvoiceRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\ScheduleInvoiceRequestDTO;
```

### ListInvoicesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\ListInvoicesRequestDTO;
```

### UpdateInvoiceRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\UpdateInvoiceRequestDTO;
```

### CancelInvoiceRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice\CancelInvoiceRequestDTO;
```
