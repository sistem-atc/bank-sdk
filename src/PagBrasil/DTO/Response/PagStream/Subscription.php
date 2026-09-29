<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;

/**
 * Assinatura PagStream — objeto canônico de GET /subscriptions/{n}, também
 * devolvido pela listagem e por pausar/reativar/cancelar/ciclo/dia/forma de
 * pagamento. Valores monetários são string decimal ("149.90"): leia como
 * decimal, nunca float. Datas em Y-m-d.
 *
 * @property list<SubscriptionProduct> $products
 */
final class Subscription implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<SubscriptionProduct> $products */
    public function __construct(
        public readonly ?string $subscriptionNumber = null,
        public readonly ?string $status = null,
        public readonly ?string $amount = null,
        #[ArrayOf(SubscriptionProduct::class)]
        public readonly array $products = [],
        public readonly ?ShippingInfo $shippingInfo = null,
        public readonly ?string $cancellationDate = null,
        public readonly ?string $nextBillingDate = null,
        public readonly ?Cycle $billingCycle = null,
        public readonly ?Cycle $shippingCycle = null,
        public readonly ?int $renewalsProcessed = null,
        public readonly ?int $renewalsOverdue = null,
        public readonly ?int $renewalsLimit = null,
        public readonly ?Payment $payment = null,
        public readonly ?string $cancellationReason = null,
        public readonly ?string $canceledBy = null,
        public readonly ?int $billingDay = null,
        public readonly ?bool $allowEditItem = null,
        public readonly ?string $cancellationEffectiveDate = null,
        public readonly ?string $customName = null,
        public readonly ?Customer $customer = null,
        public readonly ?Address $billingAddress = null,
        public readonly ?Address $shippingAddress = null,
    ) {}

    public function statusEnum(): ?SubscriptionStatus
    {
        return $this->status === null ? null : SubscriptionStatus::tryFrom($this->status);
    }
}
