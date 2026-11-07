<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

use SistemAtc\Banks\Support\AuthToken;

/**
 * Contrato mínimo de um gateway de pagamento. Os produtos (pedidos, payout,
 * assinaturas…) variam de gateway pra gateway e ficam só no connector concreto.
 */
interface GatewayConnector
{
    public static function make(): self;

    /** Autentica no gateway e devolve o token vigente. */
    public function auth(BankIntegration $integration): AuthToken;
}
