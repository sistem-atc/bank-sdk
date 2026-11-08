<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseEventDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Bill;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Account;

class BillWebhookDTO extends BaseEventDTO
{

    use CastToArray;

    public function __construct(
        ?string $id = null,
        ?WebhookEventAsaas $event = null,
        ?string $dateCreated = null,
        ?Account $account = null,
        public readonly ?Bill $bill = null,
    ) {
        parent::__construct($id, $event, $dateCreated, $account);
    }

    public static function fromArray(array $data): static
    {
        $params = static::getBaseParams($data);

        return new static(
            ...$params,
            bill: isset($data['bill']) ? Bill::fromArray($data['bill']) : null,
        );
    }
}