<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Support;

use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Resolve host e autorizador por FAMÍLIA de produto do Bradesco.
 *
 * O catálogo se divide em duas famílias, com host e OAuth distintos:
 *
 *   - 'open_api': Cobrança, Cobrança Híbrida (QR), Pagamento de boletos,
 *     Arrecadação, Saldo/Extrato, TED, Débito Automático/Veicular.
 *     Host openapi.bradesco.com.br; token em /auth/server-mtls/v2/token com
 *     client_id/client_secret NO CORPO.
 *
 *   - 'pix': Pix (geração de QR Code, transferências). Host
 *     qrpix.bradesco.com.br; token em /v2/oauth/token com credenciais em
 *     Basic Auth.
 */
final class BradescoHosts
{
    public const FAMILY_OPEN_API = 'open_api';

    public const FAMILY_PIX = 'pix';

    /** Host base da família, conforme o ambiente da integração. */
    public static function resolve(string $family, BankIntegration $integration): string
    {
        $env = self::env($integration);
        $key = $family === self::FAMILY_PIX ? 'pix' : 'default';

        $host = config("banks.bradesco.hosts.{$key}.{$env}")
            ?? config("banks.bradesco.hosts.default.{$env}");

        return rtrim((string) $host, '/');
    }

    /** URL completa do autorizador (token) da família. */
    public static function tokenUrl(string $family, BankIntegration $integration): string
    {
        $path = (string) config('banks.bradesco.oauth.'.self::familyKey($family).'.path');

        return self::resolve($family, $integration).'/'.ltrim($path, '/');
    }

    /** Onde as credenciais viajam no grant: 'body' ou 'basic'. */
    public static function credentialsMode(string $family): string
    {
        return (string) config('banks.bradesco.oauth.'.self::familyKey($family).'.credentials', 'body');
    }

    private static function familyKey(string $family): string
    {
        return $family === self::FAMILY_PIX ? 'pix' : 'open_api';
    }

    private static function env(BankIntegration $integration): string
    {
        return ($integration->isSandbox() || config('banks.sandbox', true)) ? 'sandbox' : 'production';
    }
}
