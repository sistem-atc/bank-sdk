<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Transfer;

use SistemAtc\Banks\Gateways\Asaas\Enums\TransferType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusTransfer;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\BankAccount;

final class TranferAnotherResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?TransferType $type = null,
        public readonly ?string $dateCreated = null,
        public readonly ?float $value = null,
        public readonly ?float $netValue = null,
        public readonly ?StatusTransfer $status = null,
        public readonly ?float $transferFee = null,
        public readonly ?string $effectiveDate = null,
        public readonly ?string $scheduleDate = null,
        public readonly ?string $endToEndIdentifier = null,
        public readonly ?bool $authorized = null,
        public readonly ?string $failReason = null,
        public readonly ?string $externalReference = null,
        public readonly ?string $transactionReceiptUrl = null,
        public readonly ?TransferType $operationType = null,
        public readonly ?string $description = null,
        public readonly ?string $recurring = null,
        public readonly ?BankAccount $bankAccount = null,
    ) {}
}