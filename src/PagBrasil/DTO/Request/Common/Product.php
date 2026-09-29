<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Item do campo `products` (JSON) da API clássica e da criação de
 * assinatura. Valores como NÚMERO JSON, como no exemplo da doc
 * ({"sku":"…","amount":0.00,"quantity":0,"discount":0}).
 */
final class Product implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly string $sku,
        public readonly float $amount,
        public readonly int $quantity = 1,
        public readonly float $discount = 0.0,
        /** Só no Link de Pagamento. */
        public readonly ?string $imageLink = null,
    ) {}
}
