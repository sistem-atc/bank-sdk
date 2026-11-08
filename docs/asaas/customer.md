# Metodos de Customer (Clientes)

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Customer.php`.

## Indice

- [createNewCustomer](#createnewcustomer)
- [listCustomers](#listcustomers)
- [retrieveSingleCustomer](#retrievesinglecustomer)
- [updateExistingCustomer](#updateexistingcustomer)
- [removeCustomer](#removecustomer)
- [restoreRemovedCustomer](#restoreremovedcustomer)
- [retrieveNotificationsFromCustomer](#retrievenotificationsfromcustomer)

## createNewCustomer

```php
Asaas::make()->customer($integration)->createNewCustomer(
    CustomerRequestDTO $customer
): CustomerCreateResponseDTO
```

## listCustomers

```php
Asaas::make()->customer($integration)->listCustomers(
    ListCustomerRequestDTO $queryParams
): ListCustomerResponseDTO
```

## retrieveSingleCustomer

```php
Asaas::make()->customer($integration)->retrieveSingleCustomer(string $id): CustomerCreateResponseDTO
```

## updateExistingCustomer

```php
Asaas::make()->customer($integration)->updateExistingCustomer(
    CustomerRequestDTO $customer
): CustomerCreateResponseDTO
```

## removeCustomer

```php
Asaas::make()->customer($integration)->removeCustomer(string $id): RemoveCustomerResponseDTO
```

## restoreRemovedCustomer

```php
Asaas::make()->customer($integration)->restoreRemovedCustomer(string $id): CustomerCreateResponseDTO
```

## retrieveNotificationsFromCustomer

```php
Asaas::make()->customer($integration)->retrieveNotificationsFromCustomer(
    string $id
): RetrieveNotificationCustomerResponseDTO
```

## Referencia

- [Documentacao Oficial - Clientes](https://docs.asaas.com/docs/clientes)

## Como montar os DTOs

### CustomerRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Customer\CustomerRequestDTO;

$dto = CustomerRequestDTO::fromArray([
    'name' => 'Cliente Exemplo',
    'cpfCnpj' => '12345678909',
    'email' => 'cliente@empresa.com',
]);
```

### ListCustomerRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Customer\ListCustomerRequestDTO;

$dto = ListCustomerRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```
