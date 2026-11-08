<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class CommercialInfoExpiration implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?bool $isExpired = null,
        public readonly ?string $scheduledDate = null,
    ) {}
}