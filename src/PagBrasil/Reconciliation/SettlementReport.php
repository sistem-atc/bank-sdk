<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Reconciliation;

use DateTimeImmutable;
use InvalidArgumentException;
use SistemAtc\Banks\PagBrasil\DTO\Response\Reconciliation\SettlementEntry;

/**
 * Leitor do relatório de liquidação da PagBrasil (CSV baixado em Dashboard >
 * Reports > Settlement Reports). NÃO há API pra ele — é a única fonte das
 * taxas por transação (fixa, variável, antecipação e impostos).
 *
 *   $linhas = SettlementReport::parse(file_get_contents('report000_31-07-2023.csv'));
 *
 * Formato (conferido no exemplo oficial da doc): `;` como separador, tudo
 * entre aspas, uma linha de cabeçalho. As colunas são localizadas pelo NOME
 * do cabeçalho (com os nomes da doc como alias), não pela posição.
 *
 * Armadilhas do arquivo real:
 *   - decimal ora com ponto ("8885.00"), ora com vírgula ("77,09") no MESMO
 *     arquivo → normalizado pra ponto;
 *   - data ora MM/DD/YYYY (o padrão da API), ora DD/MM/YYYY ("17/04/2023") →
 *     mantida crua; date() resolve quando dá e devolve null na ambiguidade
 *     irresolúvel só se o valor for inválido nos dois formatos;
 *   - "-" no lugar de nome/e-mail/documento em lançamentos da PagBrasil → null.
 */
final class SettlementReport
{
    /** Cabeçalho do arquivo (e o nome da doc) → campo do DTO. */
    private const COLUMNS = [
        'order number' => 'order',
        'submission date' => 'submission_date',
        'payment method' => 'payment_method',
        'order status' => 'order_status',
        'end customer name' => 'customer_name',
        'end customer e-mail address' => 'customer_email',
        'product name' => 'product_name',
        'amount due to be paid in brl by end customer' => 'amount_brl',
        'amount paid in brl by end customer' => 'amount_paid',
        'payment date' => 'payment_date',
        'refund date' => 'refund_date',
        'amount refunded in brl to end customer' => 'amount_refunded',
        'total payment fees and taxes' => 'processing_fee',
        'processing fee in brl' => 'processing_fee',
        'extra parameters' => 'param_url',
        'recurring' => 'recurring',
        'installments' => 'installments',
        'end customer tax id' => 'customer_taxid',
        'authentication' => 'authentication',
        'fixed fee' => 'fixed_fee',
        'variable fee' => 'variable_fee',
        'anticipation fee' => 'anticipation_fee',
        'taxes' => 'taxes',
    ];

    private const MONEY = [
        'amount_brl', 'amount_paid', 'amount_refunded', 'processing_fee',
        'fixed_fee', 'variable_fee', 'anticipation_fee', 'taxes',
    ];

    private const DASH_IS_NULL = ['customer_name', 'customer_email', 'customer_taxid'];

    /**
     * @return list<SettlementEntry>
     */
    public static function parse(string $csv): array
    {
        if (! mb_check_encoding($csv, 'UTF-8')) {
            $csv = mb_convert_encoding($csv, 'UTF-8', 'ISO-8859-1');
        }

        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv) ?? $csv; // BOM

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csv);
        rewind($handle);

        $header = fgetcsv($handle, null, ';', '"', '');

        if (! is_array($header)) {
            fclose($handle);

            return [];
        }

        $fields = array_map(fn ($name) => self::COLUMNS[strtolower(trim((string) $name))] ?? null, $header);

        if (! in_array('order', $fields, true)) {
            fclose($handle);

            throw new InvalidArgumentException('PagBrasil: CSV sem a coluna "Order number" — não é um Settlement Report.');
        }

        $entries = [];

        while (($row = fgetcsv($handle, null, ';', '"', '')) !== false) {
            if ($row === [null] || $row === ['']) {
                continue; // linha em branco
            }

            $data = [];
            foreach ($fields as $i => $field) {
                if ($field !== null) {
                    $data[$field] = self::clean($field, $row[$i] ?? null);
                }
            }

            $entries[] = SettlementEntry::fromArray($data);
        }

        fclose($handle);

        return $entries;
    }

    /**
     * Data do relatório: tenta MM/DD/YYYY (padrão da PagBrasil) e, se o
     * "mês" passar de 12, DD/MM/YYYY. Datas com dia e mês ≤ 12 ficam no
     * padrão MM/DD — a ambiguidade não tem como ser resolvida pelo arquivo.
     */
    public static function date(?string $value): ?DateTimeImmutable
    {
        if ($value === null || ! preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', trim($value), $m)) {
            return null;
        }

        [$first, $second, $year] = [(int) $m[1], (int) $m[2], (int) $m[3]];

        if (checkdate($first, $second, $year)) {
            return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $first, $second));
        }

        if (checkdate($second, $first, $year)) {
            return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $second, $first));
        }

        return null;
    }

    private static function clean(string $field, ?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        if ($value === null || $value === '') {
            return null;
        }

        if ($value === '-' && in_array($field, self::DASH_IS_NULL, true)) {
            return null;
        }

        if (in_array($field, self::MONEY, true)) {
            return self::money($value);
        }

        return $value;
    }

    /** "77,09" / "1.234,56" / "8885.00" → "77.09" / "1234.56" / "8885.00". */
    private static function money(string $value): string
    {
        if (str_contains($value, ',')) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        }

        return $value;
    }
}
