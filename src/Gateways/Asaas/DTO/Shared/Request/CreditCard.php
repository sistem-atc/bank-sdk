<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class CreditCard implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $holderName,
        public readonly string $number,
        public readonly string $expiryMonth,
        public readonly string $expiryYear,
        public readonly string $ccv,
    ) {}
}
