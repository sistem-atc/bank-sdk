<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class SalesSimulatorRequestsDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly float $value,
        public readonly ?int $installmentCount = null,
        /** @var \SistemAtc\Banks\Gateways\Asaas\Enums\BillingTypeSimulate[] */ public readonly array $billingTypes,
    ) {}
}