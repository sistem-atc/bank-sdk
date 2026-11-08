<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class SubscriptionCheckout implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $cycle = null,
        public readonly ?string $nextDueDate = null,
        public readonly ?string $endDate = null,
    ) {}
}