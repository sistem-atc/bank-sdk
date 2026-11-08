<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Customer implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?int $addressNumber = null,
        public readonly ?string $complement = null,
        public readonly ?string $province = null,
        public readonly ?string $postalCode = null,
        public readonly ?int $city = null,
    ) {}
}
