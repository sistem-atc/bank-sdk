<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\PagBrasilIntegration;

/**
 * Clientes HTTP da PagBrasil. Sem token nem mTLS: a autenticação é o par
 * pbtoken + secret, que vai no corpo (API clássica) ou em headers (PagStream).
 */
final class HttpClientFactory
{
    /**
     * API clássica: POST x-www-form-urlencoded. Os campos `secret`/`pbtoken`
     * entram no corpo em cada chamada (FormMethods), não aqui.
     */
    public static function form(PagBrasilIntegration $integration): PendingRequest
    {
        return self::base($integration)->asForm();
    }

    /** API REST do PagStream (v2): JSON, credenciais em headers. */
    public static function rest(PagBrasilIntegration $integration): PendingRequest
    {
        return self::base($integration)
            ->withHeaders([
                'pbtoken' => $integration->getPbToken(),
                'secret' => $integration->getSecretPhrase(),
            ])
            ->acceptJson()
            ->asJson();
    }

    private static function base(PagBrasilIntegration $integration): PendingRequest
    {
        return Http::baseUrl(PagBrasilHosts::resolve($integration))
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10));
    }
}
