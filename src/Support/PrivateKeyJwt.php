<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Support;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Monta um `client_assertion` no formato private_key_jwt (RFC 7523) — o método
 * de autenticação de cliente OAuth que o Itaú (e algumas APIs Bradesco) aceitam
 * como alternativa ao client_secret: em vez de mandar o segredo, o cliente
 * ASSINA um JWT com sua chave privada, e o banco valida com a chave pública
 * publicada (jwks_uri).
 *
 * Claims (RFC 7523 §2.2): iss=sub=client_id, aud=URL do token endpoint, jti
 * único (anti-replay), iat/exp curtos. Assinatura RS256.
 */
final class PrivateKeyJwt
{
    /**
     * @param  string  $privateKeyPem  conteúdo PEM da chave privada (ou path via file://).
     * @param  int|null  $now  epoch (injetável pra teste determinístico).
     */
    public static function assertion(
        string $clientId,
        string $audience,
        string $privateKeyPem,
        ?string $passphrase = null,
        int $ttl = 300,
        ?int $now = null,
        ?string $jti = null,
    ): string {
        $now ??= time();

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss' => $clientId,
            'sub' => $clientId,
            'aud' => $audience,
            'jti' => $jti ?? (string) Str::uuid(),
            'iat' => $now,
            'exp' => $now + $ttl,
        ];

        $signingInput = self::b64(self::json($header)).'.'.self::b64(self::json($claims));

        $key = openssl_pkey_get_private($privateKeyPem, $passphrase ?? '');
        if ($key === false) {
            throw new RuntimeException('private_key_jwt: chave privada inválida ou passphrase incorreta.');
        }

        $signature = '';
        if (! openssl_sign($signingInput, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('private_key_jwt: falha ao assinar o assertion.');
        }

        return $signingInput.'.'.self::b64($signature);
    }

    private static function json(array $data): string
    {
        return (string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Base64URL sem padding (RFC 7515 §2). */
    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
