<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class QrCode implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $encodedImage = null,
        public readonly ?string $payload = null,
    ) {}
}