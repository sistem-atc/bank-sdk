<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\AuthToken;
use SistemAtc\Banks\Support\MtlsOptions;
use SistemAtc\Banks\Support\PrivateKeyJwt;

/**
 * OAuth2 client_credentials do Itaú — a Fase 2 ("handshake do token") do fluxo
 * de certificado dinâmico. Ref.: https://devportal.itau.com.br/certificado-dinamico
 *
 * O fluxo completo tem 3 fases:
 *
 *   Fase 0 — PROVISIONAMENTO (1×, renovável a cada 365 dias): gera par RSA 2048
 *     + CSR (CN=client_id, SHA512), envia em POST
 *     `sts.itau.com.br/seguranca/v1/certificado/solicitacao` (Bearer token
 *     temporário) e recebe `certificado.crt` + `client_secret` (UUID, exibido 1×).
 *     Renovação em `/seguranca/v2/certificado/renovacao`. É um módulo à parte
 *     (CertificateProvisioning) — esta classe assume o certificado JÁ validado.
 *
 *   Fase 2 — TOKEN (esta classe, a cada ~5min): POST `sts.itau.com.br/api/oauth/token`
 *     com mTLS usando o par PEM `.crt`+`.key` (via MtlsOptions) e corpo
 *     form-urlencoded grant_type=client_credentials + client_id + client_secret.
 *     Resposta: access_token (JWT RS256) válido 300s. Sem refresh_token —
 *     reautentica ao expirar.
 *
 *   Fase 3 — NEGÓCIO: Bearer + mTLS nas APIs (Endpoints/*).
 *
 * Diferença vs Bradesco: token vem de um host STS SEPARADO da base_url da API,
 * e o certificado é PEM cert+key (não .pfx).
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
            ->post($tokenUrl, self::grantParams($integration, $tokenUrl));

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

    /**
     * Monta os parâmetros do grant client_credentials conforme o método de
     * autenticação de cliente configurado:
     *
     *   - client_secret (default): manda client_id + client_secret no corpo.
     *   - private_key_jwt: manda um client_assertion (JWT RS256 assinado com a
     *     chave privada do certificado), sem expor segredo. A chave usada é a
     *     `.key` do certificado (getCertificate()->keyPath); se o app usar uma
     *     chave de assinatura separada, o host a expõe em settings['jwt_key_pem'].
     *
     * @return array<string, string>
     */
    private static function grantParams(BankIntegration $integration, string $tokenUrl): array
    {
        $base = [
            'grant_type' => 'client_credentials',
            'client_id' => $integration->getClientId(),
        ];

        if (self::method($integration) !== 'private_key_jwt') {
            return $base + ['client_secret' => $integration->getClientSecret()];
        }

        $settings = $integration->getBankSettings();
        $keyPem = $settings['jwt_key_pem'] ?? null;
        $passphrase = $settings['jwt_key_passphrase'] ?? null;

        // Fallback: a própria chave privada do certificado mTLS.
        if ($keyPem === null && ($cert = $integration->getCertificate()) !== null && $cert->keyPath !== null) {
            $keyPem = @file_get_contents($cert->keyPath) ?: null;
            $passphrase ??= $cert->password;
        }

        if ($keyPem === null) {
            throw new BankAuthenticationException(
                'Itaú private_key_jwt: chave de assinatura ausente (settings.jwt_key_pem ou keyPath do certificado).',
                bank: 'itau',
            );
        }

        return $base + [
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => PrivateKeyJwt::assertion(
                clientId: $integration->getClientId(),
                audience: $tokenUrl,
                privateKeyPem: $keyPem,
                passphrase: $passphrase,
            ),
        ];
    }

    private static function method(BankIntegration $integration): string
    {
        return (string) ($integration->getBankSettings()['auth_method']
            ?? config('banks.itau.auth_method', 'client_secret'));
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
