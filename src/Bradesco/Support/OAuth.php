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
 * OAuth2 client_credentials do Bradesco ("Modelo de autenticação MTLS" do
 * Bradesco Developers), sempre sobre TLS mútuo.
 *
 * São DOIS autorizadores, com colocação de credencial diferente — confirmado
 * nas specs OpenAPI de cada produto:
 *
 *   - open_api → POST {host}/auth/server-mtls/v2/token
 *       form: grant_type + client_id + client_secret   (credenciais no CORPO)
 *
 *   - pix      → POST {host}/v2/oauth/token
 *       header Authorization: Basic base64(client_id:client_secret)
 *       form: grant_type=client_credentials             (só o grant no corpo)
 *
 * Ambos respondem {access_token, token_type, expires_in}. Não há
 * refresh_token — reautentica quando expira.
 */
final class OAuth
{
    public static function authenticate(
        BankIntegration $integration,
        string $family = BradescoHosts::FAMILY_OPEN_API,
    ): AuthToken {
        $tokenUrl = BradescoHosts::tokenUrl($family, $integration);
        $mode = BradescoHosts::credentialsMode($family);

        $obtainedAt = time();

        $request = Http::asForm()
            ->withOptions(MtlsOptions::forIntegration($integration))
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10));

        $body = ['grant_type' => 'client_credentials'];

        if ($mode === 'basic') {
            $request = $request->withBasicAuth(
                $integration->getClientId(),
                $integration->getClientSecret(),
            );
        } else {
            $body['client_id'] = $integration->getClientId();
            $body['client_secret'] = $integration->getClientSecret();
        }

        $response = $request->post($tokenUrl, $body);

        $data = $response->json() ?? [];

        if ($response->failed() || empty($data['access_token'])) {
            Log::error('Bradesco client_credentials falhou', [
                'family' => $family,
                'status' => $response->status(),
                'error' => $data['error'] ?? null,
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);

            throw new BankAuthenticationException(
                "Falha na autenticação Bradesco ({$family}): "
                .($data['error_description'] ?? $data['error'] ?? 'HTTP '.$response->status()),
                bank: 'bradesco',
            );
        }

        return AuthToken::fromArray([
            'access_token' => (string) $data['access_token'],
            'expires_in' => (int) ($data['expires_in'] ?? 3600),
            'token_type' => (string) ($data['token_type'] ?? 'Bearer'),
            'obtained_at' => $obtainedAt,
        ]);
    }

    /** Host base da família (mantido por compat com o factory). */
    public static function baseUrl(
        BankIntegration $integration,
        string $family = BradescoHosts::FAMILY_OPEN_API,
    ): string {
        return BradescoHosts::resolve($family, $integration);
    }
}
