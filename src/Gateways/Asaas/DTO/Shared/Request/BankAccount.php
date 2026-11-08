<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\BankAccountType;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class BankAccount implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?BankCode $bank = null,
        public readonly ?string $accountName = null,
        public readonly string $ownerName,
        public readonly ?string $ownerBirthDate = null,
        public readonly string $cpfCnpj,
        public readonly string $agency,
        public readonly string $account,
        public readonly string $accountDigit,
        public readonly ?BankAccountType $bankAccountType = null,
        public readonly ?string $ispb = null,
    ) {}
}
