<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class GenerateInstallmentBookletRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $sort = null,
        public readonly ?string $order = null,
    ) {}
}