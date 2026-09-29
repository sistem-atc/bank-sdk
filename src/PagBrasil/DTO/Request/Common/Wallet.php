<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Carteira digital: `type` AP (Apple Pay), GP (Google Pay) ou SP (Samsung
 * Pay) + o JSON COMPLETO devolvido pela autorização da carteira. Com
 * carteira, não se mandam os dados do cartão.
 */
final class Wallet implements RequestPayload
{
    use SerializesRequest;

    public function __construct(
        public readonly string $type,
        public readonly string $payload,
    ) {}

    public function toArray(): array
    {
        return ['wallet_type' => $this->type, 'wallet_payload' => $this->payload];
    }
}
