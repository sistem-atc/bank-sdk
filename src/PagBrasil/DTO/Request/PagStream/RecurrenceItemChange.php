<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Alteração de uma linha da recorrência — ao menos um campo. Quantidade 0
 * não remove (use removerItem); `discount` percentual.
 */
final class RecurrenceItemChange implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly ?int $quantity = null,
        #[Money]
        public readonly int|float|string|null $price = null,
        #[Money]
        public readonly int|float|string|null $discount = null,
    ) {
        if ($quantity === null && $price === null && $discount === null) {
            throw new InvalidArgumentException('PagBrasil: informe quantity, price e/ou discount.');
        }

        if ($quantity !== null && ($quantity < 1 || $quantity > 999999)) {
            throw new InvalidArgumentException('PagBrasil: quantidade de 1 a 999999.');
        }
    }
}
