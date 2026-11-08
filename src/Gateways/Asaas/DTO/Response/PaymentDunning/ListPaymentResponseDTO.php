<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDunning;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusFinance;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Enums\BillingTypeEscrow;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\DunningSimulate;

final class ListPaymentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $payment = null,
        public readonly ?string $customer = null,
        public readonly ?float $value = null,
        public readonly ?StatusFinance $status = null,
        public readonly ?BillingTypeEscrow $billingType = null,
        public readonly ?string $dueDate = null,
        #[ArrayOf(DunningSimulate::class)] public readonly ?array $typeSimulations = null,
    ) {}
}