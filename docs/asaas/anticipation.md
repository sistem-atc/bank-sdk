# Metodos de Antecipacao (Anticipation)

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Anticipation.php`.

## Indice

- [retrieveSingleAnticipation](#retrievesingleanticipation)
- [requestAnticipation](#requestanticipation)
- [listAnticipations](#listanticipations)
- [simulateAnticipation](#simulateanticipation)
- [updateStatusAutomaticAnticipation](#updatestatusautomaticanticipation)
- [retrieveStatusAutomaticAnticipation](#retrievestatusautomaticanticipation)
- [retrieveAnticipationLimits](#retrieveanticipationlimits)
- [cancelAnticipation](#cancelanticipation)

## retrieveSingleAnticipation

```php
Asaas::make()->anticipation($integration)->retrieveSingleAnticipation(
    string $anticipationId
): RetrieveAnticipationResponseDTO
```

## requestAnticipation

```php
Asaas::make()->anticipation($integration)->requestAnticipation(
    RequestAnticipationRequestDTO $data
): RetrieveAnticipationResponseDTO
```

## listAnticipations

```php
Asaas::make()->anticipation($integration)->listAnticipations(
    ListAnticipationFilterRequestDTO $queryParams
): ListAnticipationResponseDTO
```

## simulateAnticipation

```php
Asaas::make()->anticipation($integration)->simulateAnticipation(
    SimulateAnticipationRequestDTO $data
): SimulateAnticipationResponseDTO
```

## updateStatusAutomaticAnticipation

```php
Asaas::make()->anticipation($integration)->updateStatusAutomaticAnticipation(
    UpdateAutomaticAnticipationRequestDTO $data
): AutomaticAnticipationConfigResponseDTO
```

## retrieveStatusAutomaticAnticipation

```php
Asaas::make()->anticipation($integration)->retrieveStatusAutomaticAnticipation(): AutomaticAnticipationConfigResponseDTO
```

## retrieveAnticipationLimits

```php
Asaas::make()->anticipation($integration)->retrieveAnticipationLimits(): RetrieveAnticipationLimitsResponseDTO
```

## cancelAnticipation

```php
Asaas::make()->anticipation($integration)->cancelAnticipation(
    string $anticipationId
): RetrieveAnticipationResponseDTO
```

## Referencia

- [Documentacao Oficial - Antecipacoes](https://docs.asaas.com/docs/antecipacao)

## Como montar os DTOs

### RequestAnticipationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\RequestAnticipationRequestDTO;

$dto = RequestAnticipationRequestDTO::fromArray([
    // Ex.: 'payment' => 'pay_xxx',
    // Ex.: 'installment' => 'ins_xxx',
]);
```

### ListAnticipationFilterRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\ListAnticipationFilterRequestDTO;

$dto = ListAnticipationFilterRequestDTO::fromArray([
    'offset' => 0,
    'limit' => 50,
]);
```

### SimulateAnticipationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\SimulateAnticipationRequestDTO;

$dto = SimulateAnticipationRequestDTO::fromArray([
    // Ex.: 'payment' => 'pay_xxx',
]);
```

### UpdateAutomaticAnticipationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation\UpdateAutomaticAnticipationRequestDTO;

$dto = UpdateAutomaticAnticipationRequestDTO::fromArray([
    // Ex.: 'enabled' => true,
    // Ex.: 'type' => 'BALANCE',
]);
```
