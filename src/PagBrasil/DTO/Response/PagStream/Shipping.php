<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Envio agendado. `shippingId` é único só dentro da sua agenda (primeira
 * compra × renovação). `status`: pending, completed, canceled.
 *
 * @property list<ShippingItem> $items
 */
final class Shipping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<ShippingItem> $items */
    public function __construct(
        public readonly ?int $shippingId = null,
        public readonly ?int $recurrenceNumber = null,
        public readonly ?string $scheduledFor = null,
        public readonly ?string $amount = null,
        public readonly ?string $status = null,
        public readonly ?string $totalAmount = null,
        public readonly ?int $numShipping = null,
        public readonly ?int $totalShippings = null,
        #[ArrayOf(ShippingItem::class)]
        public readonly array $items = [],
    ) {}
}
