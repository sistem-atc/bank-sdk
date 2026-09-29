<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Support;

use DateTimeImmutable;

/**
 * A API clássica da PagBrasil devolve datas no formato AMERICANO MM/DD/YYYY
 * (`submission_date`, `payment_date`, `refund_date`…), enquanto Pix
 * Automático, Payout e PagStream usam YYYY-MM-DD. Os DTOs guardam a string
 * como veio; isto converte sem ambiguidade (10/12/2010 é 12 de outubro).
 */
final class Dates
{
    public static function parse(?string $value): ?DateTimeImmutable
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        foreach (['!m/d/Y', '!Y-m-d', 'Y-m-d H:i:s', DATE_ATOM] as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $value);

            if ($date !== false && DateTimeImmutable::getLastErrors() === false) {
                return $date;
            }
        }

        return null;
    }
}
