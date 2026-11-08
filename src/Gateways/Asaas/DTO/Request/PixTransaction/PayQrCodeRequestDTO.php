<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\QrCode;

class PayQrCodeRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly QrCode $qrCode,
        public readonly float $value,
        public readonly ?string $description = null,
        public readonly ?string $scheduleDate = null,
    ) {}
}