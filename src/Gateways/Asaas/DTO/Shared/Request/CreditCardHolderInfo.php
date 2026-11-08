<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class CreditCardHolderInfo implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $cpfCnpj,
        public readonly string $postalCode,
        public readonly string $addressNumber,
        public readonly string $phone,
        public readonly ?string $addressComplement = null,
        public readonly ?string $mobilePhone = null,
    ) {}
}