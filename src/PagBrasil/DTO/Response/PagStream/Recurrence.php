<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Snapshot de UMA recorrência (ciclo) da assinatura — devolvido pelas
 * operações de recorrência (itens, desconto, reagendar, pular/despular).
 *
 * `amount` é a soma das linhas JÁ líquida do desconto de cada item, mas NÃO
 * do desconto da recorrência (`discount`, percentual): o valor cobrado é
 * round(amount × (1 − discount/100), 2), calculado na hora da cobrança.
 * `status`: pending, processed, completed ou canceled.
 *
 * @property list<RecurrenceCharge> $chargeHistory
 * @property list<RecurrenceItem> $products
 */
final class Recurrence implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<RecurrenceCharge>  $chargeHistory
     * @param  list<RecurrenceItem>  $products
     */
    public function __construct(
        public readonly ?string $subscriptionNumber = null,
        public readonly ?int $recurrenceNumber = null,
        public readonly ?Cycle $billingCycle = null,
        public readonly ?Cycle $shippingCycle = null,
        public readonly ?Address $billingAddress = null,
        public readonly ?Address $shippingAddress = null,
        public readonly ?string $renewalDate = null,
        public readonly ?string $cancellationDate = null,
        public readonly ?string $amount = null,
        public readonly ?string $discount = null,
        public readonly ?string $status = null,
        public readonly ?bool $locked = null,
        public readonly ?bool $skipped = null,
        public readonly ?bool $paused = null,
        public readonly ?int $chargeAttempts = null,
        public readonly ?Payment $payment = null,
        #[ArrayOf(RecurrenceCharge::class)]
        public readonly array $chargeHistory = [],
        #[ArrayOf(RecurrenceItem::class)]
        public readonly array $products = [],
    ) {}
}
