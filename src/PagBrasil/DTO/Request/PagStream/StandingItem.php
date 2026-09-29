<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\PagStream;

use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Item fixo da assinatura (API v1 — vale pras recorrências FUTURAS).
 * `amountBrl` = total da linha (já com quantidade e desconto); `discount` é
 * VALOR em R$ (na API v2, desconto de item é percentual).
 */
final class StandingItem implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        #[Money]
        public readonly int|float|string|null $amountBrl = null,
        public readonly ?int $quantity = null,
        #[Money]
        public readonly int|float|string|null $discount = null,
    ) {}
}
