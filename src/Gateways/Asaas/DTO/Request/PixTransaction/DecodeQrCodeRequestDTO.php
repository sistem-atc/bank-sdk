<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class DecodeQrCodeRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $payload,
        public readonly ?float $changeValue = null,
        public readonly ?string $expectedPaymentDate = null,
    ) {}
}