<?php

namespace SistemAtc\Banks\Gateways\Asaas\Bases;

use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseWebhookEventDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Account;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;

abstract class BaseEventDTO extends BaseWebhookEventDTO
{

    use CastToArray;

    protected function __construct(
        ?string $id = null, 
        ?WebhookEventAsaas $event = null, 
        ?string $dateCreated = null, 
        ?Account $account = null)
    {
        parent::__construct($id, $event, $dateCreated, $account);
    }

    public static function fromArray(array $data): static
    {
        $params = static::getBaseParams($data);
        return new static(...$params);
    }

    protected static function getBaseParams(array $data): array
    {
        $rawId = $data['id'] ?? null;
        return [
            'id' => is_string($rawId) ? explode('&', $rawId)[0] : $rawId,
            'event' => isset($data['event']) ? WebhookEventAsaas::tryFrom($data['event']) : null,
            'dateCreated' => $data['dateCreated'] ?? null,
            'account' => isset($data['account']) ? Account::fromArray($data['account']) : null,
        ];
    }

}
