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
 * Uma linha do relatório de Vendas diárias do SFTP
 * (`dailyreport_DD_MM_YYYY.csv`): a venda com os dados da adquirente (NSU,
 * TID, autorização, bandeira). É o que concilia a venda com o extrato da
 * adquirente; o dinheiro do repasse está na Remessa.
 *
 * `cardNumber` vem mascarado pela PagBrasil — o SDK não altera.
 */
final class DailySalesEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $order = null,
        public readonly ?string $submissionDate = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $paymentStatus = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $amountDue = null,
        public readonly ?string $amountPaid = null,
        public readonly ?string $processingFee = null,
        public readonly ?string $refundDate = null,
        public readonly ?string $amountRefunded = null,
        public readonly ?string $refundProcessingFee = null,
        public readonly ?string $chargebackReason = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $customerTaxid = null,
        public readonly ?string $transactionEntity = null,
        public readonly ?string $currency = null,
        public readonly ?string $installments = null,
        public readonly ?string $cardNumber = null,
        public readonly ?string $cardBrand = null,
        public readonly ?string $authorization = null,
        public readonly ?string $nsu = null,
        public readonly ?string $tid = null,
        public readonly ?string $authentication = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $paramUrl = null,
    ) {}

    public function status(): ?OrderStatus
    {
        return $this->paymentStatus === null ? null : OrderStatus::tryFrom($this->paymentStatus);
    }

    public function method(): ?PaymentMethod
    {
        return $this->paymentMethod === null ? null : PaymentMethod::tryFrom($this->paymentMethod);
    }

    public function installmentsCount(): ?int
    {
        return $this->installments !== null && ctype_digit($this->installments) ? (int) $this->installments : null;
    }

    /** @param 'submission_date'|'payment_date'|'refund_date' $field */
    public function date(string $field): ?DateTimeImmutable
    {
        return SettlementReport::date(match ($field) {
            'submission_date' => $this->submissionDate,
            'payment_date' => $this->paymentDate,
            'refund_date' => $this->refundDate,
            default => null,
        });
    }
}
