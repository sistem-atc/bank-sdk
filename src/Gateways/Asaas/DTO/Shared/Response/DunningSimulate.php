<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Enums\DunningType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class DunningSimulate implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?DunningType $type = null,
        public readonly ?bool $isAllowed = null,
        public readonly ?string $notAllowedReason = null,
        public readonly ?float $feeValue = null,
        public readonly ?float $netValue = null,
        public readonly ?string $startDate = null,
    ) {}
}