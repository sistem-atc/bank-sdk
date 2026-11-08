<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Items implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?int $quantity = null,
        public readonly ?float $value = null,
    ) {}
}