<?php

namespace SistemAtc\Banks\Gateways\Asaas\Contracts;

use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;

interface WebhookEventDTOInterface
{
    public function getEventType(): ?WebhookEventAsaas;
    public function getEventId(): ?string;
    public function getDateCreated(): ?string;
    public static function fromArray(array $data): self;
    public function toArray(): array;
}
