<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class ImmediateQrCode implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $conciliationIdentifier = null,
        public readonly ?string $expirationDate = null,
    ) {}
}