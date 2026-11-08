<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListWebhooksRequestDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
    ) {}
}