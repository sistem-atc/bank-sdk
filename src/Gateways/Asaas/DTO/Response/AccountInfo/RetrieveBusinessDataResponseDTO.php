<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\Enums\TypePerson;
use SistemAtc\Banks\Gateways\Asaas\Enums\TypeCompany;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\City;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusRetrieveBusinessData;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\CommercialInfoExpiration;

final class RetrieveBusinessDataResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?StatusRetrieveBusinessData $status = null,
        public readonly ?TypePerson $personType = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $name = null,
        public readonly ?string $birthDate = null,
        public readonly ?string $companyName = null,
        public readonly ?TypeCompany $companyType = null,
        public readonly ?float $incomeValue = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $mobilePhone = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $address = null,
        public readonly ?string $addressNumber = null,
        public readonly ?string $complement = null,
        public readonly ?string $province = null,
        public readonly ?City $city = null,
        public readonly ?string $denialReason = null,
        public readonly ?string $tradingName = null,
        public readonly ?string $site = null,
        public readonly ?array $availableCompanyNames = null,
        public readonly ?CommercialInfoExpiration $commercialInfoExpiration = null,
    ) {}
}