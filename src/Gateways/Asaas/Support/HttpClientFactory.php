<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\Asaas\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\AsaasIntegration;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\Environment;

/**
 * Cliente HTTP de uma conta Asaas: host do ambiente da integração e a chave de
 * API no cabeçalho `access_token`.
 */
final class HttpClientFactory
{
    public static function for(BankIntegration $integration): PendingRequest
    {
        if (! $integration instanceof AsaasIntegration) {
            throw new BankAuthenticationException(
                'Asaas: a integração precisa implementar '.AsaasIntegration::class.' (chave de API e token do webhook).',
                bank: 'asaas',
            );
        }

        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração Asaas inativa.', bank: 'asaas');
        }

        $apiKey = trim($integration->getApiKey());

        if ($apiKey === '') {
            throw new BankAuthenticationException('Asaas: chave de API não informada.', bank: 'asaas');
        }

        return Http::baseUrl(self::baseUrl($integration))
            ->withHeaders(['access_token' => $apiKey])
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10));
    }

    public static function baseUrl(BankIntegration $integration): string
    {
        $env = Environment::forIntegration($integration);

        return rtrim((string) config("banks.asaas.base_url.{$env}"), '/');
    }
}
