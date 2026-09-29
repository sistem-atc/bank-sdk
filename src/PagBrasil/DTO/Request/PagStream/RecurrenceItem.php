<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Produto avulso numa recorrência (API v2). `price` null = preço do
 * catálogo; `discount` é PERCENTUAL ("10.00" = 10%).
 */
final class RecurrenceItem implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly string $productSku,
        public readonly int $quantity,
        #[Money]
        public readonly int|float|string|null $price = null,
        #[Money]
        public readonly int|float|string|null $discount = null,
    ) {
        if ($quantity < 1 || $quantity > 999999) {
            throw new InvalidArgumentException('PagBrasil: quantidade de 1 a 999999.');
        }
    }
}
