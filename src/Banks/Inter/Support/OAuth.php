<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\AuthToken;
use SistemAtc\Banks\Support\Environment;
use SistemAtc\Banks\Support\MtlsOptions;

/**
 * OAuth2 client_credentials do Inter. Ref.: spec "Autenticação OAuth" do
 * portal developers.inter.co (POST {host}/oauth/v2/token).
 *
 *   - Corpo form-urlencoded: client_id, client_secret, grant_type e `scope`
 *     (escopos separados por ESPAÇO).
 *   - mTLS obrigatório, inclusive no sandbox: par .crt + .key baixado na tela
 *     de integrações do Internet Banking.
 *   - Token vale 1h e não tem refresh_token.
 *   - Rate limit do endpoint de token: 5 chamadas por minuto. Por isso o SDK
 *     pede UM token com todos os escopos da integração (ver scopes()) e o
 *     reaproveita até expirar (TokenRefresher), em vez de um token por produto.
 */
final class OAuth
{
    public static function authenticate(BankIntegration $integration): AuthToken
    {
        $obtainedAt = time();

        $response = Http::asForm()
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->post(self::tokenUrl($integration), [
                'client_id' => $integration->getClientId(),
                'client_secret' => $integration->getClientSecret(),
                'grant_type' => 'client_credentials',
                'scope' => implode(' ', self::scopes($integration)),
            ]);

        $data = $response->json() ?? [];

        if ($response->failed() || empty($data['access_token'])) {
            Log::error('Inter client_credentials falhou', [
                'status' => $response->status(),
                'error' => $data['error'] ?? $data['title'] ?? null,
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);

            throw new BankAuthenticationException(
                'Falha na autenticação Inter: '
                .($data['error_description'] ?? $data['detail'] ?? $data['error'] ?? $data['title'] ?? 'HTTP '.$response->status()),
                bank: 'inter',
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

    /**
     * Escopos do token: `settings['scopes']` da integração (lista ou string
     * separada por espaço) ou, sem ela, `config('banks.inter.scopes')`. O Inter
     * recusa escopo que não foi marcado na aplicação, então o host deve
     * informar só os que o app da empresa tem.
     *
     * @return list<string>
     */
    public static function scopes(BankIntegration $integration): array
    {
        $scopes = $integration->getBankSettings()['scopes'] ?? config('banks.inter.scopes', []);

        if (is_string($scopes)) {
            $scopes = preg_split('/[\s,]+/', $scopes) ?: [];
        }

        return array_values(array_unique(array_filter(array_map('strval', (array) $scopes))));
    }

    public static function tokenUrl(BankIntegration $integration): string
    {
        return self::baseUrl($integration).(string) config('banks.inter.oauth_path', '/oauth/v2/token');
    }

    /** Host do ambiente da integração (sem path de produto). */
    public static function baseUrl(BankIntegration $integration): string
    {
        $env = Environment::forIntegration($integration);

        return rtrim((string) config("banks.inter.base_url.{$env}"), '/');
    }
}
