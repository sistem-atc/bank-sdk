<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * Monta o cliente HTTP autenticado do Itau: base_url do ambiente, token
 * client_credentials válido (via TokenRefresher), certificado mTLS e os headers
 * obrigatórios do gateway. Chamado antes de cada grupo de métodos, igual ao molde.
 *
 * Headers exigidos pelo gateway Itaú em TODA chamada de API:
 *   - Authorization: Bearer <access_token>  (via withToken)
 *   - x-itau-apikey: <client_id>            (constante por integração, aqui)
 *   - x-itau-correlationID: <GUID>          (único por request — no BaseMethods)
 */
final class HttpClientFactory
{
    /**
     * @param  string|null  $baseUrl  host da API a usar. Cada produto Itaú vive
     *   num host próprio (ver ItauHosts); o connector passa o host resolvido
     *   por produto. Ausente → host 'default' (OAuth::baseUrl). O token OAuth é
     *   sempre obtido no STS (independe deste host).
     */
    public static function make(BankIntegration $integration, ?string $baseUrl = null): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração Itau inativa.', bank: 'itau');
        }

        $token = TokenRefresher::valid($integration);

        return Http::baseUrl($baseUrl ?? OAuth::baseUrl($integration))
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->withToken($token)
            ->withHeaders(['x-itau-apikey' => $integration->getClientId()])
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->acceptJson()
            ->asJson();
    }
}
