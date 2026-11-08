<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Enums\DiscountType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Fine implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?float $value = null,
        public readonly ?DiscountType $type = null,
    ) {}
}