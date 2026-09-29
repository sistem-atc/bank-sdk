<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Common;

use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;

/**
 * Cliente (pagador) — vira customer_* na API clássica. Pessoa física: nome
 * completo + CPF; jurídica: razão social + CNPJ.
 *
 * O documento é normalizado como a PagBrasil exige: só letras e dígitos,
 * maiúsculas (o CNPJ alfanumérico da IN RFB 2.229/2024 é aceito), e tem de
 * ter 11 (CPF) ou 14 (CNPJ) posições.
 */
final class Customer implements RequestPayload
{
    use SerializesRequest;

    public readonly string $taxid;

    public function __construct(
        #[JsonKey('customer_name')]
        public readonly string $name,
        #[JsonKey('customer_taxid')]
        string $taxid,
        #[JsonKey('customer_email')]
        public readonly ?string $email = null,
        /** Com DDD. */
        #[JsonKey('customer_phone')]
        public readonly ?string $phone = null,
        /** IPv4 — só com a antifraude PagShield. */
        #[JsonKey('customer_ip')]
        public readonly ?string $ip = null,
    ) {
        $this->taxid = self::taxId($taxid);
    }

    /** CPF/CNPJ só com [A-Z0-9], validado pelo tamanho. */
    public static function taxId(string $value): string
    {
        $normalized = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $value));

        if (! in_array(strlen($normalized), [11, 14], true)) {
            throw new InvalidArgumentException("PagBrasil: CPF/CNPJ inválido '{$value}' (esperado 11 ou 14 posições).");
        }

        return $normalized;
    }
}
