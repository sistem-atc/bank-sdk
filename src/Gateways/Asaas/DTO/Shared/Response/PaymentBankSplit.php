<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class PaymentBankSplit implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $identificationField = null,
        public readonly ?string $nossoNumero = null,
        public readonly ?string $barCode = null,
        public readonly ?string $bankSlipUrl = null,
        public readonly ?int $daysAfterDueDateToRegistrationCancellation = null,
    ) {}
}