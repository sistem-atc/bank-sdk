<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Support;

use Illuminate\Support\Facades\Cache;
use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Garante um access_token Inter válido antes de cada request.
 *
 * O token fica no CACHE do SDK, por integração (mesmo desenho do Bradesco),
 * porque o endpoint de token do Inter aceita só 5 chamadas por minuto: um host
 * que não persiste o token (getAccessToken() null) reautenticaria a cada
 * request e esgotaria esse limite numa paginação de extrato.
 *
 * Ordem: token do host ainda válido (getBankSettings()['token_expires_at']) →
 * cache → lock por integração + double-check → OAuth.
 */
final class TokenRefresher
{
    public static function valid(BankIntegration $integration): string
    {
        $token = $integration->getAccessToken();
        if ($token && ! self::hostTokenExpired($integration)) {
            return $token;
        }

        $key = self::cacheKey($integration);

        $cached = Cache::get($key);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $lock = Cache::lock($key.':lock', 15);

        try {
            $lock->block(10);

            $cached = Cache::get($key);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }

            $auth = OAuth::authenticate($integration);

            // Expira o cache ANTES do token de fato, pela margem de segurança.
            $margin = (int) config('banks.inter.token_safety_margin', 120);
            Cache::put($key, $auth->accessToken, max(30, $auth->expiresIn - $margin));

            $integration->updateAccessToken($auth->accessToken, $auth->expiresIn);

            return $auth->accessToken;
        } finally {
            optional($lock)->release();
        }
    }

    /** Descarta o token em cache (usado quando o Inter devolve 401/403). */
    public static function forget(BankIntegration $integration): void
    {
        Cache::forget(self::cacheKey($integration));
    }

    private static function hostTokenExpired(BankIntegration $integration): bool
    {
        $expiresAt = $integration->getBankSettings()['token_expires_at'] ?? null;

        // Sem a validade, trata como expirado: nunca usar token vencido.
        if ($expiresAt === null) {
            return true;
        }

        $margin = (int) config('banks.inter.token_safety_margin', 120);

        return time() >= ((int) $expiresAt - $margin);
    }

    private static function cacheKey(BankIntegration $integration): string
    {
        return 'inter_token:'.$integration->getIntegrationIdentifier();
    }
}
