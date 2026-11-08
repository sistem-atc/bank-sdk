<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Chargeback implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $reason = null,
    ) {}
}