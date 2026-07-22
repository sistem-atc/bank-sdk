<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\AuthToken;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * OAuth2 client_credentials do Itaú.
 *
 * Diferença central vs Bradesco: o token vem de um host de STS SEPARADO da API
 * de negócio (`banks.itau.oauth_url`), não de um path sobre a base_url. As
 * credenciais vão no corpo (client_id/client_secret) e o mTLS é obrigatório em
 * produção. Não há refresh_token — reautentica quando expira.
 */
final class OAuth
{
    public static function authenticate(BankIntegration $integration): AuthToken
    {
        $tokenUrl = self::tokenUrl($integration);

        $obtainedAt = time();

        $response = Http::asForm()
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $integration->getClientId(),
                'client_secret' => $integration->getClientSecret(),
            ]);

        $data = $response->json() ?? [];

        if ($response->failed() || empty($data['access_token'])) {
            Log::error('Itau client_credentials falhou', [
                'status' => $response->status(),
                'error' => $data['error'] ?? null,
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);

            throw new BankAuthenticationException(
                'Falha na autenticação Itaú: '
                .($data['error_description'] ?? $data['error'] ?? 'HTTP '.$response->status()),
                bank: 'itau',
            );
        }

        return AuthToken::fromArray([
            'access_token' => (string) $data['access_token'],
            'expires_in' => (int) ($data['expires_in'] ?? 300),
            'token_type' => (string) ($data['token_type'] ?? 'Bearer'),
            'scope' => $data['scope'] ?? null,
            'obtained_at' => $obtainedAt,
        ]);
    }

    /** URL do STS de token conforme o ambiente. */
    public static function tokenUrl(BankIntegration $integration): string
    {
        return (string) config('banks.itau.oauth_url.'.self::env($integration));
    }

    /** URL base da API de negócio conforme o ambiente. */
    public static function baseUrl(BankIntegration $integration): string
    {
        return rtrim((string) config('banks.itau.base_url.'.self::env($integration)), '/');
    }

    private static function env(BankIntegration $integration): string
    {
        return ($integration->isSandbox() || config('banks.sandbox', true)) ? 'sandbox' : 'production';
    }
}
