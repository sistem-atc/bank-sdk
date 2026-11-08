<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class AvailableTokenBucketCheckResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $capacity = null,
        public readonly ?int $remaining = null,
    ) {}
}