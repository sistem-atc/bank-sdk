<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Support;

/**
 * Certificado cliente (mTLS) de uma integração bancária, cobrindo os dois
 * formatos que os bancos usam:
 *
 *   - PEM cert+key SEPARADOS (Itaú "certificado dinâmico"): `certPath` aponta
 *     pro `.crt` e `keyPath` pra `.key`. O par é gerado no provisionamento do
 *     certificado dinâmico e usado no mTLS de `sts.itau.com.br`.
 *
 *   - PKCS#12 (.pfx/.p12, e-CNPJ A1 — caminho do Bradesco/Bunker): `certPath`
 *     aponta pro container e `keyPath` é null; a chave vive dentro do .pfx,
 *     protegida por `password`.
 *
 * O `password` protege a chave privada (senha do .pfx, ou passphrase da .key
 * quando cifrada).
 */
final class ClientCertificate
{
    public function __construct(
        public readonly string $certPath,
        public readonly ?string $keyPath = null,
        public readonly ?string $password = null,
    ) {}

    /** É um container PKCS#12 (.pfx/.p12) — chave embutida, sem keyPath. */
    public function isPkcs12(): bool
    {
        $ext = strtolower(pathinfo($this->certPath, PATHINFO_EXTENSION));

        return in_array($ext, ['pfx', 'p12'], true);
    }
}
