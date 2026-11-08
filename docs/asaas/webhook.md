# Metodos de Webhook

Assinaturas implementadas em `src/Gateways/Asaas/Methods/Webhook.php`.

## Indice

- [createNewWebhook](#createnewwebhook)
- [listWebhooks](#listwebhooks)
- [retrieveSingleWebhook](#retrievesinglewebhook)
- [updateExistingWebhook](#updateexistingwebhook)
- [removeWebhook](#removewebhook)
- [removeWebhookBackoff](#removewebhookbackoff)

## createNewWebhook

```php
Asaas::make()->webhook($integration)->createNewWebhook(
    CreateRequestDTO $data
): WebhookResponseDTO
```

## listWebhooks

```php
Asaas::make()->webhook($integration)->listWebhooks(
    ListWebhooksRequestDTO $data
): ListWebhookResponseDTO
```

## retrieveSingleWebhook

```php
Asaas::make()->webhook($integration)->retrieveSingleWebhook(
    string $id
): WebhookResponseDTO
```

## updateExistingWebhook

```php
Asaas::make()->webhook($integration)->updateExistingWebhook(
    string $id,
    UpdateWebhookRequestDTO $data
): WebhookResponseDTO
```

## removeWebhook

```php
Asaas::make()->webhook($integration)->removeWebhook(string $id): DeleteWebhookResponseDTO
```

## removeWebhookBackoff

```php
Asaas::make()->webhook($integration)->removeWebhookBackoff(string $id): bool
```

## Como montar os DTOs

### CreateRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Webhook\CreateRequestDTO;
```

### ListWebhooksRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Webhook\ListWebhooksRequestDTO;
```

### UpdateWebhookRequestDTO

```php
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Webhook\UpdateWebhookRequestDTO;
```
