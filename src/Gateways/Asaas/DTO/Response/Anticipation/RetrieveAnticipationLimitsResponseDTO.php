<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Anticipation;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\AnticipationBankSlip;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\AnticipationCreditCard;

final class RetrieveAnticipationLimitsResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?AnticipationCreditCard $creditCard = null,
        public readonly ?AnticipationBankSlip $bankSlip = null,
    ) {}
}