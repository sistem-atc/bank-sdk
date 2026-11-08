<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class CellPhoneCredits implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $bonus = null,
        public readonly ?float $minValue = null,
        public readonly ?float $maxValue = null,
        ) {}
}