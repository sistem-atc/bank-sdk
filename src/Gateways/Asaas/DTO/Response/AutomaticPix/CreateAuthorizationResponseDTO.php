<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Enums\Frequency;
use SistemAtc\Banks\Gateways\Asaas\Enums\OriginType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusAutomaticPix;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\ImmediateQrCode;

final class CreateAuthorizationResponseDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $minLimitValue = null,
        public readonly ?string $cancellationDate = null,
        public readonly ?string $cancellationReason = null,
        public readonly ?string $contractId = null,
        public readonly ?string $customerId = null,
        public readonly ?string $description = null,
        public readonly ?string $finishDate = null,
        public readonly ?Frequency $frequency = null,
        public readonly ?string $endToEndIdentifier = null,
        public readonly ?string $startDate = null,
        public readonly ?StatusAutomaticPix $status = null,
        public readonly ?float $value = null,
        public readonly ?string $payload = null,
        public readonly ?string $encodedImage = null,
        public readonly ?ImmediateQrCode $immediateQrCode = null,
        public readonly ?OriginType $originType = null,
        public readonly ?string $subscriptionId = null,
    ) {}
}