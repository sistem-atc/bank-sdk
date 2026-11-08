<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\FinancialTransaction;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\TransactionType;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ExtractResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?float $value = null,
        public readonly ?float $balance = null,
        public readonly ?TransactionType $type = null,
        public readonly ?string $date = null,
        public readonly ?string $description = null,
        public readonly ?string $paymentId = null,
        public readonly ?string $splitId = null,
        public readonly ?string $transferId = null,
        public readonly ?string $anticipationId = null,
        public readonly ?string $billId = null,
        public readonly ?string $invoiceId = null,
        public readonly ?string $paymentDunningId = null,
        public readonly ?string $creditBureauReportId = null,
    ) {}
}