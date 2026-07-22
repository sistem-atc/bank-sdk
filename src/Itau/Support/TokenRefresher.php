<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Support;

use Illuminate\Support\Facades\Cache;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Garante um access_token Itau válido antes de cada request.
 *
 * Diferença vs marketplaces: client_credentials NÃO tem refresh_token — quando
 * expira, reautentica com client_id/secret (OAuth::authenticate). O lock por
 * integração evita que N requests concorrentes disparem N autenticações; o
 * double-check reaproveita o token que outra request já renovou.
 *
 * Validade: o host informa a expiração via getBankSettings()['token_expires_at']
 * (epoch s) — persistido no updateAccessToken. Sem esse dado, trata como
 * expirado (reautentica) pra nunca usar token vencido.
 */
final class TokenRefresher
{
    public static function valid(BankIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        $lock = Cache::lock('itau_token_'.$integration->getIntegrationIdentifier(), 15);

        try {
            $lock->block(10);

            // Double-check: outra request pode ter reautenticado sob o lock.
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
        $margin = (int) config('banks.itau.token_safety_margin', 60);

        // Se o host expõe um AuthToken/instante de expiração, usa-o.
        $expiresAt = $integration->getBankSettings()['token_expires_at'] ?? null;

        if ($expiresAt === null) {
            return true;
        }

        return time() >= ((int) $expiresAt - $margin);
    }
}
