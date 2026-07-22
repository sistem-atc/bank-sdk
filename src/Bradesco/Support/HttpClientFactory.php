<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * Monta o cliente HTTP autenticado do Bradesco: base_url do ambiente, token
 * client_credentials válido (via TokenRefresher) e o certificado mTLS anexado
 * ao transporte. Chamado antes de cada grupo de métodos, igual ao molde.
 */
final class HttpClientFactory
{
    public static function make(BankIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração Bradesco inativa.', bank: 'bradesco');
        }

        $token = TokenRefresher::valid($integration);

        return Http::baseUrl(OAuth::baseUrl($integration))
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->withToken($token)
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->acceptJson()
            ->asJson();
    }
}
