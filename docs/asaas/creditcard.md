# Metodos de CreditCard

Assinaturas implementadas em `src/Gateways/Asaas/Methods/CreditCard.php`.

## Indice

- [tokenization](#tokenization)

## tokenization

```php
Asaas::make()->creditCard($integration)->tokenization(
    CreditCardTokenizationRequestDTO $data
): CommonCreditCard
```


## Como montar os DTOs

### CreditCardTokenizationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback\CreditCardTokenizationRequestDTO;

$dto = CreditCardTokenizationRequestDTO::fromArray([
    // Ex.: 'creditCardNumber' => '4111111111111111',
    // Ex.: 'creditCardHolderName' => 'Nome do Portador',
]);
```
