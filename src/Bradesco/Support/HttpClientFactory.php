<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * Monta o cliente HTTP autenticado do Bradesco: host da FAMÍLIA do produto
 * (open_api ou pix), token válido do autorizador correspondente e o
 * certificado mTLS anexado ao transporte.
 */
final class HttpClientFactory
{
    public static function make(
        BankIntegration $integration,
        string $family = BradescoHosts::FAMILY_OPEN_API,
    ): PendingRequest {
        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração Bradesco inativa.', bank: 'bradesco');
        }

        $token = TokenRefresher::valid($integration, $family);

        return Http::baseUrl(BradescoHosts::resolve($family, $integration))
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->withToken($token)
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->acceptJson()
            ->asJson();
    }
}
