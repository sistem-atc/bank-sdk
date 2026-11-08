<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Split implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $walletId = null,
        public readonly ?float $fixedValue = null,
        public readonly ?float $percentualValue = null,
        public readonly ?string $status = null,
        public readonly ?string $refusalReason = null,
        public readonly ?string $externalReference = null,
        public readonly ?string $description = null,
        public readonly ?string $totalFixedValue = null,
    ) {}
}