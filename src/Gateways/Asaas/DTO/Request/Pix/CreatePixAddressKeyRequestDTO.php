<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\PixAddressKeyType;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class CreatePixAddressKeyRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly PixAddressKeyType $type,
    ) {}
}