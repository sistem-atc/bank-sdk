<?php

namespace SistemAtc\Banks\Gateways\Asaas\Bases;

use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook\Account;
use SistemAtc\Banks\Gateways\Asaas\Contracts\WebhookEventDTOInterface;

abstract class BaseWebhookEventDTO implements WebhookEventDTOInterface
{
    protected function __construct(
        public readonly ?string $id,
        public readonly ?WebhookEventAsaas $event,
        public readonly ?string $dateCreated,
        public readonly ?Account $account,
    ) {}

    public function getEventType(): ?WebhookEventAsaas
    {
        return $this->event ?? null;
    }

    public function getEventId(): ?string
    {
        return $this->id ?? null;
    }

    public function getDateCreated(): ?string
    {
        return $this->dateCreated ?? null;
    }

    public function getAccount(): ?Account
    {
        return $this->account ?? null;
    }

}
