<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\DisabledReason;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusSplitSubscription;

class Split implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $walletId = null,
        public readonly ?float $fixedValue = null,
        public readonly ?float $percentualValue = null,
        public readonly ?string $externalReference = null,
        public readonly ?string $description = null,
        public readonly ?StatusSplitSubscription $status = null,
        public readonly ?DisabledReason $disabledReason = null,
    ) {}
}
