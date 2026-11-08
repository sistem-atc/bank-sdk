<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Enums\BillingType;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\CycleSubscription;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Fine;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Split;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Discount;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Interest;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Callback;

final class CreateSubscriptionRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $customer,
        public readonly BillingType $billingType,
        public readonly float $value,
        public readonly string $nextDueDate,
        public readonly ?Discount $discount = null,
        public readonly ?Interest $interest = null,
        public readonly ?Fine $fine = null,
        public readonly CycleSubscription $cycle,
        public readonly ?string $description = null,
        public readonly ?string $endDate = null,
        public readonly ?int $maxPayments = null,
        public readonly ?string $externalReference = null,
        #[ArrayOf(Split::class)] public readonly ?array $split = null,
        public readonly ?Callback $callback = null,
    ) {}
}