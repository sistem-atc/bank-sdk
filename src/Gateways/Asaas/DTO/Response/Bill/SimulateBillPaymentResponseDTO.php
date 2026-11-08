<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Bill;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\BankSlipInfo;

final class SimulateBillPaymentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $minimumScheduleDate = null,
        public readonly ?int $fee = null,
        #[ArrayOf(BankSlipInfo::class)] public readonly ?string $bankSlipInfo = null,
    ) {}
}