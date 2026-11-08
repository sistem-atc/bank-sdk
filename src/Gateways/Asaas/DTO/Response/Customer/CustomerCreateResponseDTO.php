<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Customer;

use SistemAtc\Banks\Gateways\Asaas\Enums\TypePerson;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class CustomerCreateResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $mobilePhone = null,
        public readonly ?string $address = null,
        public readonly ?string $addressNumber = null,
        public readonly ?string $complement = null,
        public readonly ?string $province = null,
        public readonly ?int $city = null,
        public readonly ?string $cityName = null,
        public readonly ?string $state = null,
        public readonly ?string $country = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?TypePerson $personType = null,
        public readonly ?bool $deleted = null,
        public readonly ?string $additionalEmails = null,
        public readonly ?string $externalReference = null,
        public readonly ?bool $notificationDisabled = null,
        public readonly ?string $observations = null,
        public readonly ?string $foreignCustomer = null,
    ) {}
}
