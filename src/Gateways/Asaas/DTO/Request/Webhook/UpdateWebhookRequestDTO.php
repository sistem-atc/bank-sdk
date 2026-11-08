<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Enums\SendType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\WebhookEventAsaas;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class UpdateWebhookRequestDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $url = null,
        public readonly ?SendType $sendType = null,
        public readonly ?bool $enabled = null,
        public readonly ?bool $interrupted = null,
        public readonly ?string $authToken = null,
        /** @var WebhookEventAsaas[] */ public readonly ?array $events = null,
    ) {}
}