<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Enums\PixAddressKeyType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class ExternalAccount implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $ispb = null,
        public readonly ?string $ispbName = null,
        public readonly ?string $name = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $addressKey = null,
        public readonly ?PixAddressKeyType $addressKeyType = null,
    ) {}
}