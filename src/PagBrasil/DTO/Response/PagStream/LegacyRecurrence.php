<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Recorrência (pedido) na assinatura v1. `paymentDate` em MM/DD/YYYY;
 * `orderStatus` no código de 2 letras da API clássica (OrderStatus).
 *
 * @property list<LegacyProduct> $products
 */
final class LegacyRecurrence implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<LegacyProduct> $products */
    public function __construct(
        public readonly ?string $order = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $orderStatus = null,
        public readonly ?string $link = null,
        public readonly ?string $productName = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $amountOriginal = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $customerEmail = null,
        #[ArrayOf(LegacyProduct::class)]
        public readonly array $products = [],
    ) {}
}
