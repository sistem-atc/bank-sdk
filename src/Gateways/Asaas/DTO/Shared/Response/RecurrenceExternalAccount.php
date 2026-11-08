<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class RecurrenceExternalAccount implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $financialInstitutionName = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $pixKey = null,
    ) {}
}