<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Enums\BillingType;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\CycleSubscription;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusSubscription;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Fine;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Split;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Discount;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Interest;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Callback;

final class UpdateSubscriptionRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?BillingType $billingType = null,
        public readonly ?StatusSubscription $status = null,
        public readonly ?string $nextDueDate = null,
        public readonly ?Discount $discount = null,
        public readonly ?Interest $interest = null,
        public readonly ?Fine $fine = null,
        public readonly ?CycleSubscription $cycle = null,
        public readonly ?string $description = null,
        public readonly ?string $endDate = null,
        public readonly ?bool $updatePendingPayments = null,
        public readonly ?string $externalReference = null,
        #[ArrayOf(Split::class)] public readonly ?array $split = null,
        public readonly ?Callback $callback = null,
    ) {}
}