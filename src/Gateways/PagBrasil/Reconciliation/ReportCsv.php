<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reconciliation;

use InvalidArgumentException;

/**
 * Leitura comum dos CSVs da PagBrasil (liquidação do Dashboard e os três
 * relatórios do SFTP): `;` como separador, aspas opcionais, uma linha de
 * cabeçalho, UTF-8 (com fallback pra Latin-1 e BOM removido).
 *
 * As colunas são localizadas pelo NOME do cabeçalho, nunca pela posição: a
 * PagBrasil já publicou o mesmo relatório com colunas em ordem diferente.
 *
 * @internal
 */
final class ReportCsv
{
    /**
     * @param  array<string, string>  $columns  cabeçalho em minúsculas → campo do DTO
     * @param  list<string>  $money  campos de valor (normalizados pra decimal com ponto)
     * @param  list<string>  $dashIsNull  campos em que "-" significa vazio
     * @param  string  $required  campo que identifica o relatório (sem ele, erro)
     * @return list<array<string, string|null>>
     */
    public static function rows(string $csv, array $columns, array $money, array $dashIsNull, string $required, string $reportName): array
    {
        if (! mb_check_encoding($csv, 'UTF-8')) {
            $csv = mb_convert_encoding($csv, 'UTF-8', 'ISO-8859-1');
        }

        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv) ?? $csv; // BOM

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csv);
        rewind($handle);

        $header = fgetcsv($handle, null, ';', '"', '');

        if (! is_array($header) || $header === [null]) {
            fclose($handle);

            return [];
        }

        $fields = array_map(fn ($name) => $columns[self::normalizeHeader((string) $name)] ?? null, $header);

        if (! in_array($required, $fields, true)) {
            fclose($handle);

            throw new InvalidArgumentException("PagBrasil: CSV sem a coluna de \"{$required}\" — não é um {$reportName}.");
        }

        $rows = [];

        while (($row = fgetcsv($handle, null, ';', '"', '')) !== false) {
            if ($row === [null] || $row === [''] || implode('', array_map('trim', array_map('strval', $row))) === '') {
                continue; // linha em branco
            }

            $data = [];
            foreach ($fields as $i => $field) {
                // Dois cabeçalhos podem ser alias do mesmo campo: vale o
                // primeiro preenchido (isset() é falso pra null).
                if ($field !== null && ! isset($data[$field])) {
                    $data[$field] = self::clean($row[$i] ?? null, in_array($field, $money, true), in_array($field, $dashIsNull, true));
                }
            }

            $rows[] = $data;
        }

        fclose($handle);

        return $rows;
    }

    /** "Order number " / "ORDER NUMBER" → "order number". */
    public static function normalizeHeader(string $name): string
    {
        $name = str_replace(["\u{2019}", "\u{2018}", '`'], "'", $name); // apóstrofo tipográfico ("PagBrasil’s")

        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($name)) ?? '');
    }

    private static function clean(?string $value, bool $money, bool $dashIsNull): ?string
    {
        $value = $value === null ? null : trim($value);

        if ($value === null || $value === '') {
            return null;
        }

        if ($value === '-' && ($dashIsNull || $money)) {
            return null;
        }

        return $money ? self::money($value) : $value;
    }

    /** "77,09" / "1.234,56" / "8885.00" → "77.09" / "1234.56" / "8885.00". */
    public static function money(string $value): string
    {
        if (str_contains($value, ',')) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        }

        return $value;
    }
}
