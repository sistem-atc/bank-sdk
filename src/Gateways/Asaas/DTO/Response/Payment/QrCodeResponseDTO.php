<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class QrCodeResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?bool $success = null,
        public readonly ?string $encodedImage = null,
        public readonly ?string $payload = null,
        public readonly ?string $expirationDate = null,
        public readonly ?string $description = null,
    ) {}
}