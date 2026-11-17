<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Reconciliation;

use SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation\DailySalesEntry;

/**
 * Leitor do relatório de Vendas diárias do SFTP (`dailyreport_DD_MM_YYYY.csv`):
 * vendas com os dados da adquirente (NSU, TID, autorização).
 *
 *   $linhas = DailySalesReport::parse($csv);   // list<DailySalesEntry>
 *
 * Layout do "Guia de relatórios via SFTP — Nacional" v1.0 (out/2026): 25
 * colunas, `;`, UTF-8, decimal com ponto, datas MM/DD/YYYY.
 */
final class DailySalesReport
{
    private const COLUMNS = [
        "pagbrasil's order number" => 'order',
        'pagbrasil order number' => 'order',
        'order number' => 'order',
        'submission date' => 'submission_date',
        'payment method' => 'payment_method',
        'payment status' => 'payment_status',
        'payment date' => 'payment_date',
        'amount due' => 'amount_due',
        'amount paid' => 'amount_paid',
        'processing fee' => 'processing_fee',
        'refund/chargeback date' => 'refund_date',
        'amount refunded/chargeback' => 'amount_refunded',
        'refund/chargeback processing fee' => 'refund_processing_fee',
        'chargeback reason' => 'chargeback_reason',
        "customer's e-mail address" => 'customer_email',
        "customer's tax id" => 'customer_taxid',
        'transaction entity' => 'transaction_entity',
        'currency' => 'currency',
        'installments' => 'installments',
        'card number' => 'card_number',
        'card brand' => 'card_brand',
        'authorization' => 'authorization',
        'nsu' => 'nsu',
        'tid' => 'tid',
        'authentication' => 'authentication',
        'error code' => 'error_code',
        'extra parameters' => 'param_url',
    ];

    private const MONEY = ['amount_due', 'amount_paid', 'processing_fee', 'amount_refunded', 'refund_processing_fee'];

    private const DASH_IS_NULL = ['customer_email', 'customer_taxid'];

    /** @return list<DailySalesEntry> */
    public static function parse(string $csv): array
    {
        $rows = ReportCsv::rows($csv, self::COLUMNS, self::MONEY, self::DASH_IS_NULL, 'order', 'relatório de Vendas diárias');

        return array_map(fn (array $data) => DailySalesEntry::fromArray($data), $rows);
    }
}
