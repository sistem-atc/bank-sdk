<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subaccount;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ApiKeySubAccountRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $expirationDate = null,
    ) {}
}