<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subaccount;

use SistemAtc\Banks\Gateways\Asaas\Enums\TypeCompany;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Webhook\CreateRequestDTO;

final class SubAccountRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $loginEmail = null,
        public readonly string $cpfCnpj,
        public readonly ?string $birthDate = null,
        public readonly ?TypeCompany $companyType = null,
        public readonly ?string $phone = null,
        public readonly string $mobilePhone,
        public readonly ?string $site = null,
        public readonly float $incomeValue,
        public readonly string $address,
        public readonly string $addressNumber,
        public readonly ?string $complement = null,
        public readonly string $province,
        public readonly string $postalCode,
        #[ArrayOf(CreateRequestDTO::class)] public readonly ?array $webhooks = null,
    ) {}
}