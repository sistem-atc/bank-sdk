<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Support;

use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Traduz o certificado mTLS da integração em opções de transporte pro cliente
 * HTTP (Guzzle/cURL). Compartilhado por todos os bancos — o TLS mútuo é
 * idêntico; só muda a base_url e o fluxo de token de cada banco.
 *
 * Cobre os dois formatos (ver ClientCertificate):
 *   - PEM cert+key SEPARADOS (Itaú): `cert` = .crt e `ssl_key` = .key. É o que
 *     o handshake de `sts.itau.com.br` exige — sem `ssl_key`, o cURL fecha o
 *     TLS sem a chave privada e o handshake falha.
 *   - PKCS#12 (.pfx/.p12, Bradesco): `cert` = [container, senha] + cURL
 *     SSLCERTTYPE=P12 (a chave vive dentro do container).
 */
final class MtlsOptions
{
    /**
     * @return array<string, mixed> opções pra Http::withOptions(); vazio quando
     *                              não há certificado (sandbox sem mTLS).
     */
    public static function forIntegration(BankIntegration $integration): array
    {
        $cert = $integration->getCertificate();

        if ($cert === null || $cert->certPath === '') {
            return [];
        }

        $password = $cert->password;

        // PKCS#12: chave embutida no container; cURL precisa saber o tipo.
        if ($cert->isPkcs12()) {
            return [
                'cert' => $password !== null && $password !== ''
                    ? [$cert->certPath, $password]
                    : $cert->certPath,
                'curl' => [CURLOPT_SSLCERTTYPE => 'P12'],
            ];
        }

        // PEM: certificado e chave privada em arquivos separados.
        $options = ['cert' => $cert->certPath];

        if ($cert->keyPath !== null && $cert->keyPath !== '') {
            $options['ssl_key'] = $password !== null && $password !== ''
                ? [$cert->keyPath, $password]
                : $cert->keyPath;
        }

        return $options;
    }
}
