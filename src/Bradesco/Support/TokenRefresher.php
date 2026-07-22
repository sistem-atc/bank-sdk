<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Support;

use Illuminate\Support\Facades\Cache;
use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Garante um access_token Bradesco válido antes de cada request.
 *
 * Particularidade do Bradesco: há DOIS autorizadores (open_api e pix), então
 * uma mesma integração tem DOIS tokens vivos ao mesmo tempo — e o contract
 * BankIntegration guarda só um. Por isso o token vigente é mantido em cache
 * POR FAMÍLIA (chave integração+família), com TTL derivado do expires_in.
 * O `updateAccessToken` do host continua sendo chamado para persistência e
 * observabilidade, mas não é a fonte de verdade aqui.
 *
 * O lock por chave evita que N requests concorrentes disparem N autenticações;
 * o double-check reaproveita o token que outra request já obteve.
 */
final class TokenRefresher
{
    public static function valid(
        BankIntegration $integration,
        string $family = BradescoHosts::FAMILY_OPEN_API,
    ): string {
        $key = self::cacheKey($integration, $family);

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

            $auth = OAuth::authenticate($integration, $family);

            // Expira o cache ANTES do token de fato, pela margem de segurança.
            $margin = (int) config('banks.bradesco.token_safety_margin', 60);
            $ttl = max(30, $auth->expiresIn - $margin);

            Cache::put($key, $auth->accessToken, $ttl);
            $integration->updateAccessToken($auth->accessToken, $auth->expiresIn);

            return $auth->accessToken;
        } finally {
            optional($lock)->release();
        }
    }

    /** Descarta o token da família (usado quando o banco devolve 401/403). */
    public static function forget(BankIntegration $integration, string $family): void
    {
        Cache::forget(self::cacheKey($integration, $family));
    }

    private static function cacheKey(BankIntegration $integration, string $family): string
    {
        return 'bradesco_token:'.$integration->getIntegrationIdentifier().':'.$family;
    }
}
