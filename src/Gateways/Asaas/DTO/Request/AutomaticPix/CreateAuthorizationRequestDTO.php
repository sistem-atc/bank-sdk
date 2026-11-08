<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Enums\Frequency;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\ImmediateQrCode;

final class CreateAuthorizationRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly Frequency $frequency,
        public readonly string $contractId,
        public readonly string $startDate,
        public readonly ?string $finishDate = null,
        public readonly ?float $value = null,
        public readonly ?string $description = null,
        public readonly string $customerId,
        public readonly ImmediateQrCode $immediateQrCode,
        public readonly ?int $minLimitValue = null,
    ) {}
}