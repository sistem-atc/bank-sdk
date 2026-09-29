<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Produto do catálogo de assinatura (GET /products, PATCH /products/{sku}).
 * Produto nunca configurado: status inactive, flags false, displayOrder null,
 * rules [].
 *
 * @property list<ProductRule> $rules
 */
final class Product implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<ProductRule> $rules */
    public function __construct(
        public readonly ?string $sku = null,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $imageLink = null,
        public readonly ?string $amount = null,
        public readonly ?string $status = null,
        public readonly ?bool $onCustomerArea = null,
        public readonly ?bool $singlePurchase = null,
        public readonly ?bool $orderTrigger = null,
        public readonly ?int $displayOrder = null,
        #[ArrayOf(ProductRule::class)]
        public readonly array $rules = [],
    ) {}
}
