<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Pix;

use SistemAtc\Banks\Gateways\Asaas\Enums\FormatQrCode;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class CreateQRCodeStaticRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $addressKey = null,
        public readonly ?string $description = null,
        public readonly ?float $value = null,
        public readonly ?FormatQrCode $format = null,
        public readonly ?float $expirationDate = null,
        public readonly ?int $expirationSeconds = null,
        public readonly ?bool $allowsMultiplePayments = null,
        public readonly ?string $externalReference = null,
    ) {}
}