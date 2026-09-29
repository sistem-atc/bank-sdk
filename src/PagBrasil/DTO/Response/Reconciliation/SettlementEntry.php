<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Reconciliation;

use DateTimeImmutable;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\PagBrasil\Enums\OrderStatus;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;
use SistemAtc\Banks\PagBrasil\Reconciliation\SettlementReport;

/**
 * Uma linha do relatório de liquidação (Dashboard > Reports > Settlement
 * Reports). Valores já normalizados pra decimal com ponto ("249.27"); datas
 * ficam como vieram — o arquivo MISTURA MM/DD/YYYY e DD/MM/YYYY (ver
 * SettlementReport::date()).
 *
 * `processingFee` é o TOTAL cobrado na linha e é ele que se concilia. As
 * colunas de detalhe (fixedFee, variableFee, anticipationFee, taxes) NÃO
 * fecham o total em estorno e chargeback: no exemplo oficial, o estorno
 * 00000866 cobra 3,00 a mais que a soma e cada chargeback (linha 3000xxxx)
 * cobra 25,00 com o detalhe zerado — tarifa de estorno/chargeback sem coluna
 * própria. Linhas com
 * paymentMethod I / orderStatus CD são débitos/créditos lançados pela
 * própria PagBrasil (ex.: "Extra fee"), não vendas.
 */
final class SettlementEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $order = null,
        public readonly ?string $submissionDate = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $orderStatus = null,
        public readonly ?string $customerName = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $productName = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $amountPaid = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $refundDate = null,
        public readonly ?string $amountRefunded = null,
        public readonly ?string $processingFee = null,
        public readonly ?string $paramUrl = null,
        public readonly ?string $recurring = null,
        public readonly ?string $installments = null,
        public readonly ?string $customerTaxid = null,
        public readonly ?string $authentication = null,
        public readonly ?string $fixedFee = null,
        public readonly ?string $variableFee = null,
        public readonly ?string $anticipationFee = null,
        public readonly ?string $taxes = null,
    ) {}

    public function status(): ?OrderStatus
    {
        return $this->orderStatus === null ? null : OrderStatus::tryFrom($this->orderStatus);
    }

    public function method(): ?PaymentMethod
    {
        return $this->paymentMethod === null ? null : PaymentMethod::tryFrom($this->paymentMethod);
    }

    /** Lançamento da PagBrasil (tarifa avulsa, ajuste), não transação de cliente. */
    public function isAdjustment(): bool
    {
        return $this->status() === OrderStatus::PagBrasilAdjustment || $this->method() === PaymentMethod::PagBrasilAdjustment;
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
