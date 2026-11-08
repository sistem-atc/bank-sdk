<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Transfer;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class AccountTransferResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $agency = null,
        public readonly ?string $account = null,
        public readonly ?string $accountDigit = null,
    ) {}
}