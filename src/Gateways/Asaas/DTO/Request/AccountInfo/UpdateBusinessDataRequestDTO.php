<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\Enums\TypePerson;
use SistemAtc\Banks\Gateways\Asaas\Enums\TypeCompany;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class UpdateBusinessDataRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?TypePerson $personType = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $birthDate = null,
        public readonly ?TypeCompany $companyType = null,
        public readonly ?string $companyName = null,
        public readonly ?float $incomeValue = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $mobilePhone = null,
        public readonly ?string $site = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $address = null,
        public readonly ?string $addressNumber = null,
        public readonly ?string $complement = null,
        public readonly ?string $province = null,
    ) {}
}