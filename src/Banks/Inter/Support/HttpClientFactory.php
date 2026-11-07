<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * Cliente HTTP autenticado do Inter: host do ambiente + path base do produto
 * (`/banking/v2`, `/cobranca/v3`, `/pix/v2`), token válido, mTLS e o header
 * `x-conta-corrente` quando a integração o informa.
 *
 * `x-conta-corrente`: obrigatório só quando a aplicação do Inter está ligada a
 * MAIS DE UMA conta. Vem de settings['conta_corrente']; o Inter exige só
 * dígitos (com o DV) e sem zeros à esquerda, então o valor é normalizado aqui.
 */
final class HttpClientFactory
{
    public static function make(BankIntegration $integration, string $basePath): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração Inter inativa.', bank: 'inter');
        }

        $client = Http::baseUrl(OAuth::baseUrl($integration).$basePath)
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->withToken(TokenRefresher::valid($integration))
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->acceptJson()
            ->asJson();

        $conta = self::contaCorrente($integration);

        return $conta !== null ? $client->withHeaders(['x-conta-corrente' => $conta]) : $client;
    }

    public static function contaCorrente(BankIntegration $integration): ?string
    {
        $raw = $integration->getBankSettings()['conta_corrente'] ?? null;

        if ($raw === null || $raw === '') {
            return null;
        }

        $digits = ltrim(preg_replace('/\D/', '', (string) $raw) ?? '', '0');

        return $digits === '' ? null : $digits;
    }
}
