<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Support;

/**
 * Chave de idempotência do Pix de saída (`x-id-idempotente`). O Inter exige um
 * UUID (RFC 4122). O contrato PixEndpoint aceita qualquer `identificador` do
 * host (id do título, do lote…), então quando ele não é UUID derivamos um UUID
 * DETERMINÍSTICO dele: o mesmo identificador gera sempre o mesmo UUID, e um
 * retry não vira pagamento duplicado.
 */
final class Idempotency
{
    private const UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

    public static function uuid(string $identificador): string
    {
        $lower = strtolower(trim($identificador));

        if (preg_match(self::UUID, $lower) === 1) {
            return $lower;
        }

        // UUID v5 (SHA-1, namespace próprio do SDK) — mesmo formato do
        // Ramsey\Uuid::uuid5, sem depender da lib.
        $hash = sha1('sistem-atc/bank-sdk:inter:'.$identificador);

        return sprintf(
            '%s-%s-5%s-%s%s-%s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 13, 3),
            dechex((hexdec($hash[16]) & 0x3) | 0x8),
            substr($hash, 17, 3),
            substr($hash, 20, 12),
        );
    }
}
