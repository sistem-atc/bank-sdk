<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Checkout;

use SistemAtc\Banks\Gateways\Asaas\Enums\ChargeType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\BillingTypeCheckout;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Callback;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Customer;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Installment;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Subscription;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\ItemCheckout;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\SplitCheckout;

final class CheckoutResponseDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        /** @var BillingTypeCheckout[] */ public readonly ?array $billingTypes = null,
        /** @var ChargeType[] */ public readonly ?array $chargeTypes = null,
        public readonly ?int $minutesToExpire = null,
        public readonly ?string $externalReference = null,
        public readonly ?Callback $callback = null,
        #[CastToArray(ItemCheckout::class)] public readonly ?array $items = null,
        public readonly ?Customer $customerData = null,
        public readonly ?Subscription $subscription = null,
        public readonly ?Installment $installment = null,
        #[CastToArray(SplitCheckout::class)] public readonly ?array $splits = null,
    ) {}
}