<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseEventDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Account;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Checkout;

class CheckoutWebhookDTO extends BaseEventDTO
{

    use CastToArray;
    
    public function __construct(
        ?string $id = null,
        ?WebhookEventAsaas $event = null,
        ?string $dateCreated = null,
        ?Account $account = null,
        public readonly ?Checkout $checkout = null,
    ) {
        parent::__construct($id, $event, $dateCreated, $account);
    }

    public static function fromArray(array $data): static
    {
        $params = static::getBaseParams($data);

        return new static(
            ...$params,
            checkout: isset($data['checkout']) ? Checkout::fromArray($data['checkout']) : null,
        );
    }
}