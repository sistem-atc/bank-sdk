<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\DTO\Response\Reconciliation;

use DateTimeImmutable;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\OrderStatus;
use SistemAtc\Banks\Gateways\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\Gateways\PagBrasil\Reconciliation\SettlementReport;

/**
 * Uma linha do relatório de Remessa do SFTP
 * (`remittancereport_DD_MM_YYYY.csv`): uma transação dentro de um REPASSE da
 * PagBrasil, com o valor líquido. `remittanceDate` é o dia em que o repasse
 * foi disponibilizado — é por ela que as linhas se agrupam num repasse.
 *
 * Valores normalizados pra decimal com ponto ("249.27"); datas cruas
 * (MM/DD/YYYY) — use date().
 *
 * `netAmount` = `amountPaid` − `amountRefunded` − `totalFees` (a PagBrasil
 * entrega pronto; não recalcule). Em estorno e chargeback o líquido sai
 * negativo.
 */
final class RemittanceEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $remittanceDate = null,
        public readonly ?string $reportDate = null,
        public readonly ?string $submissionDate = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $refundDate = null,
        public readonly ?string $order = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $status = null,
        public readonly ?string $installments = null,
        public readonly ?string $description = null,
        public readonly ?string $amountPaid = null,
        public readonly ?string $amountRefunded = null,
        public readonly ?string $totalFees = null,
        public readonly ?string $netAmount = null,
        public readonly ?string $cardBrand = null,
    ) {}

    public function orderStatus(): ?OrderStatus
    {
        return $this->status === null ? null : OrderStatus::tryFrom($this->status);
    }

    public function method(): ?PaymentMethod
    {
        return $this->paymentMethod === null ? null : PaymentMethod::tryFrom($this->paymentMethod);
    }

    /** Lançamento da PagBrasil (tarifa avulsa, ajuste), não transação de cliente. */
    public function isAdjustment(): bool
    {
        return $this->orderStatus() === OrderStatus::PagBrasilAdjustment || $this->method() === PaymentMethod::PagBrasilAdjustment;
    }

    public function installmentsCount(): ?int
    {
        return $this->installments !== null && ctype_digit($this->installments) ? (int) $this->installments : null;
    }

    /** @param 'remittance_date'|'report_date'|'submission_date'|'payment_date'|'refund_date' $field */
    public function date(string $field): ?DateTimeImmutable
    {
        return SettlementReport::date(match ($field) {
            'remittance_date' => $this->remittanceDate,
            'report_date' => $this->reportDate,
            'submission_date' => $this->submissionDate,
            'payment_date' => $this->paymentDate,
            'refund_date' => $this->refundDate,
            default => null,
        });
    }
}
