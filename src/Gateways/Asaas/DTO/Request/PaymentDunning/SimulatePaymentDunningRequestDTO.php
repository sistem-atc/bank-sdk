<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class SimulatePaymentDunningRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $payment = null,
    ) {}
}
