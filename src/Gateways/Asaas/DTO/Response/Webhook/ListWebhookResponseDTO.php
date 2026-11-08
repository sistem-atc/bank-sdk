<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListWebhookResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?bool $hasMore = null,
        public readonly ?int $totalCount = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        #[ArrayOf(WebhookResponseDTO::class)] public readonly ?array $data = null,
    ) {}
}