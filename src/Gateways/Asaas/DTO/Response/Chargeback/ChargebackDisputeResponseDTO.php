<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Chargeback;

use SistemAtc\Banks\Gateways\Asaas\Enums\DisputeStatus;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ChargebackDisputeResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $chargebackId = null,
        public readonly ?DisputeStatus $status = null,
        public readonly ?array $files = null,
    ) {}
}