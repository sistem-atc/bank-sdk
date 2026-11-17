<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Enums;

use DateTimeImmutable;

/**
 * Relatórios que a PagBrasil deposita no SFTP (/reports/). O valor é o
 * prefixo do nome do arquivo: `{prefixo}{conta}_DD-MM-YYYY.csv` (ver
 * parseFilename()).
 *
 * Cada conta recebe só os relatórios configurados pelo Suporte, cada um com a
 * sua frequência (diária, semanal, quinzenal, mensal). Sem transação no
 * período, o arquivo não é gerado.
 */
enum ReportType: string
{
    /** Conciliação: transações com valores, status, tarifas e impostos (22 colunas). */
    case Reconciliation = 'report';

    /** Vendas diárias: dados da adquirente — NSU, TID, autorização (25 colunas). */
    case DailySales = 'dailyreport';

    /** Remessa: as transações de cada repasse, com o valor líquido (15 colunas). */
    case Remittance = 'remittancereport';

    public function label(): string
    {
        return match ($this) {
            self::Reconciliation => 'Conciliação',
            self::DailySales => 'Vendas diárias',
            self::Remittance => 'Remessa (repasse)',
        };
    }

    /**
     * Tipo, data e código da conta de um nome de arquivo do SFTP, ou null se o
     * nome não for de relatório conhecido.
     *
     * Formato REAL (conferido no servidor em 17/11/2026): o código da conta
     * PagBrasil vem colado ao tipo e a data usa hífen —
     * "remittancereport1898_28-10-2026.csv". O guia v1.0 descreve
     * "report_DD_MM_YYYY.csv" (sem conta, com underscore): os dois são aceitos.
     *
     *   "report1898_08-04-2026.csv"  → [Reconciliation, 2026-04-08, "1898"]
     *   "report_17_11_2026.csv"      → [Reconciliation, 2026-11-17, null]
     *
     * @return array{0: self, 1: DateTimeImmutable, 2: string|null}|null
     */
    public static function parseFilename(string $filename): ?array
    {
        $base = basename(trim($filename));

        if (! preg_match('/^([a-z]+?)(\d*)_(\d{2})[_-](\d{2})[_-](\d{4})\.csv$/i', $base, $m)) {
            return null;
        }

        $type = self::tryFrom(strtolower($m[1]));
        [$day, $month, $year] = [(int) $m[3], (int) $m[4], (int) $m[5]];

        if ($type === null || ! checkdate($month, $day, $year)) {
            return null;
        }

        return [$type, new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day)), $m[2] !== '' ? $m[2] : null];
    }
}
