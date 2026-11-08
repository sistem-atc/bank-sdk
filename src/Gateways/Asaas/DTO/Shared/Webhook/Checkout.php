<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Callback;

class Checkout implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $link = null,
        public readonly ?string $status = null,
        public readonly ?int $minutesToExpire = null,
        public readonly ?array $billingTypes = null,
        public readonly ?array $chargeTypes = null,
        public readonly ?Callback $callback = null,
        public readonly ?array $items = null,
        public readonly ?SubscriptionCheckout $subscription = null,
        public readonly ?string $installment = null,
        public readonly ?array $split = null,
        public readonly ?string $customer = null,
        public readonly ?string $customerData = null,
    ) {}
}