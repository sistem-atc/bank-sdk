<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\CycleSubscription;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Subscription implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?CycleSubscription $cycle = null,
        public readonly ?string $endDate = null,
        public readonly ?string $nextDueDate = null,
    ) {}
}