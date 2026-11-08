<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\MobilePhoneRecharge;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class RechargeRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly float $value,
        public readonly string $phoneNumber,
    ) {}
}
