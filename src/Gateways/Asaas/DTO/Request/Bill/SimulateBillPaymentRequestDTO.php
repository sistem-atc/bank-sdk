<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class SimulateBillPaymentRequestDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $identificationField = null,
        public readonly ?string $barCode = null,
    ) {}
}