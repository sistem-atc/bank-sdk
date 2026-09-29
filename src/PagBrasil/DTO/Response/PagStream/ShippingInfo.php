<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** Linha de frete da assinatura (fica fora de `products`). */
final class ShippingInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $name = null,
        public readonly ?string $price = null,
        public readonly ?string $discount = null,
    ) {}
}
