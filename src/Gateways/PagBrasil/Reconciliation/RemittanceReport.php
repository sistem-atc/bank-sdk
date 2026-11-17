<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reconciliation;

use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\RemittanceEntry;

/**
 * Leitor do relatório de Remessa do SFTP (`remittancereport{conta}_DD-MM-YYYY.csv`):
 * as transações de cada repasse, com o valor líquido.
 *
 *   $linhas = RemittanceReport::parse($csv);   // list<RemittanceEntry>
 *
 * Layout do "Guia de relatórios via SFTP — Nacional" v1.0 (out/2026): 15
 * colunas, `;`, UTF-8. O arquivo REAL (conferido em 17/11/2026) difere do
 * guia: datas ISO ("2026-10-28") e decimal com VÍRGULA ("410,90"), com BOM —
 * o leitor aceita os dois formatos. Colunas localizadas pelo nome do
 * cabeçalho.
 */
final class RemittanceReport
{
    private const COLUMNS = [
        'remittance date' => 'remittance_date',
        'report date' => 'report_date',
        'submission date' => 'submission_date',
        'payment date' => 'payment_date',
        'refund date' => 'refund_date',
        'order number' => 'order',
        'payment method' => 'payment_method',
        'status' => 'status',
        'order status' => 'status',
        'installments' => 'installments',
        'description' => 'description',
        'amount paid in brl by end customer' => 'amount_paid',
        'amount refunded in brl to end customer' => 'amount_refunded',
        'total payment fees and taxes' => 'total_fees',
        'net amount' => 'net_amount',
        'card brand' => 'card_brand',
    ];

    private const MONEY = ['amount_paid', 'amount_refunded', 'total_fees', 'net_amount'];

    /** @return list<RemittanceEntry> */
    public static function parse(string $csv): array
    {
        $rows = ReportCsv::rows($csv, self::COLUMNS, self::MONEY, [], 'remittance_date', 'relatório de Remessa');

        return array_map(fn (array $data) => RemittanceEntry::fromArray($data), $rows);
    }
}
