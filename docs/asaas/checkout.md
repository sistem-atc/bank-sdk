# Metodos de Checkout

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Checkout.php`.

## Indice

- [createCheckout](#createcheckout)
- [cancelCheckout](#cancelcheckout)

## createCheckout

```php
Asaas::make()->checkout($integration)->createCheckout(
    CreateNewCheckoutRequestDTO $data
): CheckoutResponseDTO
```

## cancelCheckout

```php
Asaas::make()->checkout($integration)->cancelCheckout(string $id): CheckoutResponseDTO
```


## Como montar os DTOs

### CreateNewCheckoutRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Checkout\CreateNewCheckoutRequestDTO;

$dto = CreateNewCheckoutRequestDTO::fromArray([
    // Campos do checkout
]);
```
