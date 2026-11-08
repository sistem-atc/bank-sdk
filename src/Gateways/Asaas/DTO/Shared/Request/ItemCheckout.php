<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class ItemCheckout implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $externalReference = null,
        public readonly ?string $description = null,
        public readonly string $imageBase64,
        public readonly string $name,
        public readonly int $quantity,
        public readonly float $value,
    ) {}
}