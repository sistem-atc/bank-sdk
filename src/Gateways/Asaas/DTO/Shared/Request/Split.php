<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Split implements DTOInterface
{
    
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $walletId,
        public readonly ?float $fixedValue = null,
        public readonly ?float $percentualValue = null,
        public readonly ?float $totalFixedValue = null,
        public readonly ?string $externalReference = null,
        public readonly ?string $description = null,
        public readonly ?string $installmentNumber = null,
    ) {}
}