<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Enums\DisputeStatus;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\ChargebackReason;
use SistemAtc\Banks\Gateways\Asaas\Enums\ChargebackStatus;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class PaymentChargeback implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $payment = null,
        public readonly ?string $installment = null,
        public readonly ?string $customerAccount = null,
        public readonly ?ChargebackStatus $status = null,
        public readonly ?ChargebackReason $reason = null,
        public readonly ?string $disputeStartDate = null,
        public readonly ?float $value = null,
        public readonly ?string $paymentDate = null,
        public readonly ?ChargebackCreditCard $creditCard = null,
        public readonly ?DisputeStatus $disputeStatus = null,
        public readonly ?string $deadlineToSendDisputeDocuments = null,
    ) {}
}