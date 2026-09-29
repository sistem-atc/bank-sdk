<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/** Item de um envio. `amount` é valor UNITÁRIO. */
final class ShippingItemInput implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly string $sku,
        public readonly int $quantity,
        #[Money]
        public readonly int|float|string $amount,
    ) {}
}
