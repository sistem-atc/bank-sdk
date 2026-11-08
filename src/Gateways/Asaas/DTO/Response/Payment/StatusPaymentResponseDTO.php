<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPayment;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class StatusPaymentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?StatusPayment $status = null,
    ) {}
}