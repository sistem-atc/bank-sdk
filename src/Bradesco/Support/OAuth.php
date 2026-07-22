<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\AuthToken;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * OAuth2 client_credentials do Bradesco Open Banking.
 *
 * Fluxo base: POST no oauth_path com `grant_type=client_credentials` e o par
 * client_id/client_secret em Basic Auth; a resposta traz access_token +
 * expires_in. Não há refresh_token — reautentica-se quando expira.
 *
 * NOTA sobre variações: algumas APIs Bradesco exigem `client_assertion` (JWT
 * RS256 assinado com a private_key do app) em vez de Basic Auth. Quando for o
 * caso da API-alvo, o host fornece a private_key via getBankSettings()
 * ['jwt_private_key'] e esta classe monta o assertion — ponto único de
 * evolução, sem mexer nos Endpoints. Por ora o grant é o Basic client_credentials.
 */
final class OAuth
{
    public static function authenticate(BankIntegration $integration): AuthToken
    {
        $tokenUrl = self::baseUrl($integration).config('banks.bradesco.oauth_path', '/auth/server/v1.1/token');

        $obtainedAt = time();

        $response = Http::asForm()
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->withBasicAuth($integration->getClientId(), $integration->getClientSecret())
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->post($tokenUrl, [
                'grant_type' => 'client_credentials',
            ]);

        $data = $response->json() ?? [];

        if ($response->failed() || empty($data['access_token'])) {
            Log::error('Bradesco client_credentials falhou', [
                'status' => $response->status(),
                'error' => $data['error'] ?? null,
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);

            throw new BankAuthenticationException(
                'Falha na autenticação Bradesco: '
                .($data['error_description'] ?? $data['error'] ?? 'HTTP '.$response->status()),
                bank: 'bradesco',
            );
        }

        return AuthToken::fromArray([
            'access_token' => (string) $data['access_token'],
            'expires_in' => (int) ($data['expires_in'] ?? 3600),
            'token_type' => (string) ($data['token_type'] ?? 'Bearer'),
            'scope' => $data['scope'] ?? null,
            'obtained_at' => $obtainedAt,
        ]);
    }

    /** URL base conforme o ambiente (produção vs homologação). */
    public static function baseUrl(BankIntegration $integration): string
    {
        $env = ($integration->isSandbox() || config('banks.sandbox', true)) ? 'sandbox' : 'production';

        return rtrim((string) config("banks.bradesco.base_url.{$env}"), '/');
    }
}
