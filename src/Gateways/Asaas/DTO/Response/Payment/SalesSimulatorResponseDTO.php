<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\SimulationPayment;

final class SalesSimulatorResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?float $value = null,
        public readonly ?SimulationPayment $creditCard = null,
        public readonly ?SimulationPayment $bankSlip = null,
        public readonly ?SimulationPayment $pix = null,
    ) {}
}