<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** Linha de produto da assinatura. `discount` é percentual em escala humana ("10.00" = 10%). */
final class SubscriptionProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sku = null,
        public readonly ?string $name = null,
        public readonly ?string $imageLink = null,
        public readonly ?string $price = null,
        public readonly ?string $discount = null,
    ) {}
}
