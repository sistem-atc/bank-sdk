<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class InternalTransferData implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?float $value = null,
        public readonly ?string $date = null,
        public readonly ?string $type = null,
    ) {}
}