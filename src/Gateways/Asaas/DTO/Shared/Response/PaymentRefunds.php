<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusRefund;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class PaymentRefunds implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $dateCreated = null,
        public readonly ?StatusRefund $status = null,
        public readonly ?float $value = null,
        public readonly ?string $endToEndIdentifier = null,
        public readonly ?string $description = null,
        public readonly ?string $effectiveDate = null,
        public readonly ?string $transactionReceiptUrl = null,
        #[ArrayOf(RefundedSplits::class)] public readonly ?array $refundedSplits = null,
        public readonly ?string $paymentId = null,
    ) {}
}