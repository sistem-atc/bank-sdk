<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListTransferRequestDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?string $dateCreated__ge = null,
        public readonly ?string $dateCreated__le = null,
        public readonly ?string $transferDate__ge = null,
        public readonly ?string $transferDate__le = null,
        public readonly ?string $type = null,
    ) {}
}