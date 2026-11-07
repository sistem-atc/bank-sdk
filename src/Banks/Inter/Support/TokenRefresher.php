<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Support;

use Illuminate\Support\Facades\Cache;
use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Garante um access_token Inter válido antes de cada request.
 *
 * Mesmo desenho do Itaú: client_credentials sem refresh_token, validade
 * persistida pelo host em getBankSettings()['token_expires_at'] (epoch s, via
 * updateAccessToken), lock por integração e double-check. Aqui o lock pesa
 * mais: o endpoint de token do Inter aceita só 5 chamadas por minuto, e N
 * requests concorrentes reautenticando juntos esgotariam esse limite.
 */
final class TokenRefresher
{
    public static function valid(BankIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        $lock = Cache::lock('inter_token_'.$integration->getIntegrationIdentifier(), 15);

        try {
            $lock->block(10);

            $token = $integration->getAccessToken();
            if ($token && ! self::isExpired($integration)) {
                return $token;
            }

            $auth = OAuth::authenticate($integration);
            $integration->updateAccessToken($auth->accessToken, $auth->expiresIn);

            return $auth->accessToken;
        } finally {
            optional($lock)->release();
        }
    }

    private static function isExpired(BankIntegration $integration): bool
    {
        $expiresAt = $integration->getBankSettings()['token_expires_at'] ?? null;

        // Sem a validade, trata como expirado: nunca usar token vencido.
        if ($expiresAt === null) {
            return true;
        }

        $margin = (int) config('banks.inter.token_safety_margin', 120);

        return time() >= ((int) $expiresAt - $margin);
    }
}
