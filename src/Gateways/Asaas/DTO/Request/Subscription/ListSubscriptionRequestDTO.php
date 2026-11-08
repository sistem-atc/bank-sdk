<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\BillingTypeEscrow;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusSubscription;

final class ListSubscriptionRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?string $customer = null,
        public readonly ?string $customerGroupName = null,
        public readonly ?BillingTypeEscrow $billingType = null,
        public readonly ?StatusSubscription $status = null,
        public readonly ?string $deletedOnly = null,
        public readonly ?string $includeDeleted = null,
        public readonly ?string $externalReference = null,
        public readonly ?string $order = null,
        public readonly ?string $sort = null,
    ) {}
}