<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Enums\AccountType;
use SistemAtc\Banks\Gateways\Asaas\Enums\TypePerson;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Receiver implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $ispb = null,
        public readonly ?string $ispbName = null,
        public readonly ?string $name = null,
        public readonly ?string $tradingName = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?TypePerson $personType = null,
        public readonly ?AccountType $accountType = null,
    ) {}
}