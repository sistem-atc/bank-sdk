# Metodos de Notification

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Notification.php`.

## Indice

- [updateExistingNotification](#updateexistingnotification)
- [updateExistingNotificationsinBatch](#updateexistingnotificationsinbatch)

## updateExistingNotification

```php
Asaas::make()->notification($integration)->updateExistingNotification(
    string $id,
    UpdateNotificationRequestDTO $data
): NotificationResponse
```

## updateExistingNotificationsinBatch

```php
Asaas::make()->notification($integration)->updateExistingNotificationsinBatch(
    UpdateNotificationBatchRequestDTO $data
): UpdateNotificationBatchResponseDTO
```


## Como montar os DTOs

### UpdateNotificationRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Notification\UpdateNotificationRequestDTO;

$dto = UpdateNotificationRequestDTO::fromArray([
    // Ex.: 'enabled' => true,
]);
```

### UpdateNotificationBatchRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Notification\UpdateNotificationBatchRequestDTO;

$dto = UpdateNotificationBatchRequestDTO::fromArray([
    // Ex.: lista de notificacoes para atualizacao em lote
]);
```
