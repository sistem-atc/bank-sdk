<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\CreditCard;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PaymentPix;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PaymentBankSplit;

final class PaymentBilingInformationResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?PaymentPix $pix = null,
        public readonly ?CreditCard $creditCard = null,
        public readonly ?PaymentBankSplit $bankSlip = null,
    ) {}
}
