<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Support;

use Illuminate\Support\Facades\Http;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Token OAuth2 (client_credentials, Basic auth) da PagBrasil —
 * POST /api/oauth/token.
 *
 * NÃO autentica as APIs de negócio (essas usam pbtoken + secret). Serve só pra
 * autorizar o PagBrasil.JS no front: o backend gera o token de curta duração
 * e o entrega ao checkout (setOAuthToken), sem expor credencial nenhuma.
 * Credenciais = client_id/client_secret do Dashboard (getClientId/Secret).
 */
final class OAuth
{
    public static function authenticate(BankIntegration $integration): AuthToken
    {
        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração PagBrasil inativa.', bank: 'pagbrasil');
        }

        if ($integration->getClientId() === '' || $integration->getClientSecret() === '') {
            throw new BankAuthenticationException(
                'PagBrasil: client_id/client_secret (OAuth do PagBrasil.JS) não cadastrados.',
                bank: 'pagbrasil',
            );
        }

        $response = Http::baseUrl(PagBrasilHosts::resolve($integration))
            ->timeout((int) config('banks.http.timeout', 30))
            ->connectTimeout((int) config('banks.http.connect_timeout', 10))
            ->withBasicAuth($integration->getClientId(), $integration->getClientSecret())
            ->asForm()
            ->acceptJson()
            ->post('/api/oauth/token', ['grant_type' => 'client_credentials']);

        $data = $response->json();

        if ($response->failed() || ! is_array($data) || empty($data['access_token'])) {
            $detail = is_array($data)
                ? ($data['error_description'] ?? $data['message'] ?? $data['error'] ?? null)
                : null;

            throw new BankAuthenticationException(
                'PagBrasil OAuth: '.(is_string($detail) && $detail !== '' ? $detail : 'HTTP '.$response->status()),
                bank: 'pagbrasil',
            );
        }

        $token = new AuthToken(
            accessToken: (string) $data['access_token'],
            expiresIn: (int) ($data['expires_in'] ?? 0),
            tokenType: isset($data['token_type']) ? (string) $data['token_type'] : 'Bearer',
            scope: isset($data['scope']) ? (string) $data['scope'] : null,
            obtainedAt: time(),
        );

        $integration->updateAccessToken($token->accessToken, $token->expiresIn);

        return $token;
    }
}
