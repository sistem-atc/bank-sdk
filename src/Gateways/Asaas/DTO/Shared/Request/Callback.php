<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Callback implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly string $successUrl,
        public readonly ?bool $autoRedirect = null,
    ) {}
}
