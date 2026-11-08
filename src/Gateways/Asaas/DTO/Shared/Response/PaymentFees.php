<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class PaymentFees implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?BankSlipFees $bankSlip = null,
        public readonly ?CreditCardFees $creditCard = null,
        public readonly ?DebitCardFees $debitCard = null,
        public readonly ?PixFees $pix = null,
    ) {}
}