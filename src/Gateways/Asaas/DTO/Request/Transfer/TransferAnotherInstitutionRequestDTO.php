<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer;

use SistemAtc\Banks\Gateways\Asaas\Enums\OperationType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\PixAddressKeyType;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Recurring;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\BankAccount;

final class TransferAnotherInstitutionRequestDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly float $value,
        public readonly ?BankAccount $bankAccount = null,
        public readonly ?OperationType $operationType = null,
        public readonly ?string $pixAddressKey = null,
        public readonly ?PixAddressKeyType $pixAddressKeyType = null,
        public readonly ?string $description = null,
        public readonly ?string $scheduleDate = null,
        public readonly ?string $externalReference = null,
        public readonly ?Recurring $recurring = null,
    ) {}
}