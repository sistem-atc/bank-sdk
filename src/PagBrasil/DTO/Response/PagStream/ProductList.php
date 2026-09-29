<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** @property list<Product> $data */
final class ProductList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<Product> $data */
    public function __construct(
        #[ArrayOf(Product::class)]
        public readonly array $data = [],
        public readonly int $page = 1,
        public readonly int $perPage = 24,
        public readonly int $total = 0,
        public readonly int $totalPages = 0,
    ) {}
}
