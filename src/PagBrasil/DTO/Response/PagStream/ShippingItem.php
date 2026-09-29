<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** Item de um envio. `amount` é valor UNITÁRIO. */
final class ShippingItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sku = null,
        public readonly ?int $quantity = null,
        public readonly ?string $amount = null,
    ) {}
}
