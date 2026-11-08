<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPix;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListAuthorizationRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?StatusPix $status = null,
        public readonly ?string $customerId = null,
    ) {}
}