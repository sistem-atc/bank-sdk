<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Orders;

use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundBankAccount;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\RefundPixKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Estorno (/api/order/refund), total ou parcial. `destination` só quando o
 * estorno pelo meio original foi REJEITADO (cartão +300 dias, Pix +90 dias,
 * "Refund must be by bank transfer"): conta ou chave Pix do próprio cliente.
 * `suspicious` alimenta a antifraude PagShield.
 */
final class Refund implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly string $order,
        #[JsonKey('amount_refunded'), Money]
        public readonly int|float|string $amount,
        #[Flatten]
        public readonly RefundBankAccount|RefundPixKey|null $destination = null,
        public readonly bool $suspicious = false,
    ) {}
}
