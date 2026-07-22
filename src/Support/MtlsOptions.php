<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Support;

use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Traduz o certificado mTLS da integração em opções de transporte pro cliente
 * HTTP (Guzzle/cURL). Compartilhado por todos os bancos — o TLS mútuo é
 * idêntico; só muda a base_url e o fluxo de token de cada banco.
 *
 * Suporta PEM (default do Guzzle) e PKCS#12 (.pfx/.p12) — neste caso setamos
 * CURLOPT_SSLCERTTYPE=P12 pra cURL ler o container com a senha. No Bunker o
 * CompanyCertificate guarda .pfx, então o caminho P12 é o quente.
 */
final class MtlsOptions
{
    /**
     * @return array<string, mixed> opções pra Http::withOptions(); vazio quando
     *                              não há certificado (sandbox sem mTLS).
     */
    public static function forIntegration(BankIntegration $integration): array
    {
        $path = $integration->getCertificatePath();

        if ($path === null || $path === '') {
            return [];
        }

        $password = $integration->getCertificatePassword();

        $options = [
            'cert' => $password !== null && $password !== ''
                ? [$path, $password]
                : $path,
        ];

        // .pfx/.p12 → PKCS#12; cURL precisa saber o tipo (PEM é o default).
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['pfx', 'p12'], true)) {
            $options['curl'] = [CURLOPT_SSLCERTTYPE => 'P12'];
        }

        return $options;
    }
}
