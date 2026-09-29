<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** Produto na assinatura v1. `discount` aqui é VALOR em R$, não percentual. */
final class LegacyProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sku = null,
        public readonly ?string $unitPrice = null,
        public readonly ?string $quantity = null,
        public readonly ?string $discount = null,
        public readonly ?string $amountTotal = null,
        public readonly ?string $category = null,
    ) {}
}
