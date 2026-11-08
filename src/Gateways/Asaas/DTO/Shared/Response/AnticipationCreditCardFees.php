<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class AnticipationCreditCardFees implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?float $detachedMonthlyFeeValue = null,
        public readonly ?float $installmentMonthlyFeeValue = null,
    ) {}
}