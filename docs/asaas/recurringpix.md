# Metodos de RecurringPix

Assinaturas implementadas em `src/Gateways/Asaas/Methods/RecurringPix.php`.

## Indice

- [listRecurrences](#listrecurrences)
- [retrieveSingleRecurrence](#retrievesinglerecurrence)
- [cancelRecurrence](#cancelrecurrence)
- [listRecurrenceItems](#listrecurrenceitems)
- [cancelRecurrenceItem](#cancelrecurrenceitem)

## listRecurrences

```php
Asaas::make()->recurringPix($integration)->listRecurrences(
    ListRecurrencesRequestDTO $data
): ListRecurrenceResponseDTO
```

## retrieveSingleRecurrence

```php
Asaas::make()->recurringPix($integration)->retrieveSingleRecurrence(
    string $id
): SingleRecurrenceResponseDTO
```

## cancelRecurrence

```php
Asaas::make()->recurringPix($integration)->cancelRecurrence(
    string $id
): SingleRecurrenceResponseDTO
```

## listRecurrenceItems

```php
Asaas::make()->recurringPix($integration)->listRecurrenceItems(
    string $id,
    ListRecurrencesItemsRequestDTO $data
): ListItemsRecurrenceResponseDTO
```

## cancelRecurrenceItem

```php
Asaas::make()->recurringPix($integration)->cancelRecurrenceItem(
    string $id
): SingleRecurrenceResponseDTO
```

## Como montar os DTOs

### ListRecurrencesRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\RecurringPix\ListRecurrencesRequestDTO;
```

### ListRecurrencesItemsRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\RecurringPix\ListRecurrencesItemsRequestDTO;
```
