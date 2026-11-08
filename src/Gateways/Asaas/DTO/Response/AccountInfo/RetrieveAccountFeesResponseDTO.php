<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\FeeValue;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PaymentFees;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\TransferFees;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\AnticipationFees;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\NotificationFees;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\CreditBureauReportFees;

final class RetrieveAccountFeesResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?PaymentFees $payment = null,
        public readonly ?TransferFees $transfer = null,
        public readonly ?NotificationFees $notification = null,
        public readonly ?CreditBureauReportFees $creditBureauReport = null,
        public readonly ?FeeValue $paymentDunning = null,
        public readonly ?FeeValue $invoice = null,
        public readonly ?AnticipationFees $anticipation = null,
    ) {}
}