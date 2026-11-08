<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Payment;

use SistemAtc\Banks\Gateways\Asaas\Enums\BillingType;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Fine;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Split;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Callback;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Discount;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Interest;

final class CreatePaymentRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly string $customer,
        public readonly BillingType $billingType,
        public readonly float $value,
        public readonly string $dueDate,
        public readonly ?string $description = null,
        public readonly ?int $daysAfterDueDateToRegistrationCancellation = null,
        public readonly ?string $externalReference = null,
        public readonly ?int $installmentCount = null,
        public readonly ?float $totalValue = null,
        public readonly ?float $installmentValue = null,
        public readonly ?Discount $discount = null,
        public readonly ?Interest $interest = null,
        public readonly ?Fine $fine = null,
        public readonly ?bool $postalService = null,
        #[ArrayOf(Split::class)] public readonly ?array $split = null,
        public readonly ?Callback $callback = null,
        public readonly ?string $pixAutomaticAuthorizationId = null,
    ) {}
}
