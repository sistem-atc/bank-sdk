<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class SimulateAnticipationRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $installment = null,
        public readonly ?string $payment = null,
    ) {}
}