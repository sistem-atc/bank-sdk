<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;

/**
 * Assinatura no formato da API v1 — resposta dos itens fixos
 * (/api/pagstream/subscription/item/add|update|delete). `status` numérico
 * legado (SubscriptionStatus::fromLegacy()).
 *
 * @property list<LegacyRecurrence> $recurrences
 * @property list<LegacyProduct> $products
 */
final class LegacySubscription implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<LegacyRecurrence>  $recurrences
     * @param  list<LegacyProduct>  $products
     */
    public function __construct(
        public readonly ?string $subscription = null,
        public readonly ?string $status = null,
        public readonly ?string $billingCycle = null,
        public readonly ?string $shippingCycle = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $numberRecurrences = null,
        public readonly ?string $limit = null,
        public readonly ?string $viewFutureCharges = null,
        public readonly ?string $nextBillingDate = null,
        public readonly ?string $cancellationDate = null,
        public readonly ?string $effectiveCancellationDate = null,
        public readonly ?string $orderToken = null,
        public readonly ?string $pixRecId = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $customerName = null,
        public readonly ?string $customerPhone = null,
        #[ArrayOf(LegacyRecurrence::class)]
        public readonly array $recurrences = [],
        #[ArrayOf(LegacyProduct::class)]
        public readonly array $products = [],
    ) {}

    public function statusEnum(): ?SubscriptionStatus
    {
        return SubscriptionStatus::fromLegacy($this->status);
    }
}
