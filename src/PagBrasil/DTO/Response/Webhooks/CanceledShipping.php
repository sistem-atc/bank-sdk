<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Envio cancelado no webhook `shipping_canceled`. `schedule`:
 * first_buy_shippings ou renewal_shippings (aí com `recurrenceNumber`).
 */
final class CanceledShipping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $shippingId = null,
        public readonly ?string $schedule = null,
        public readonly ?int $recurrenceNumber = null,
    ) {}
}
