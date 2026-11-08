<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusEvents;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class DunningEvent implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?StatusEvents $status = null,
        public readonly ?string $description = null,
        public readonly ?string $eventDate = null,
    ) {}
}