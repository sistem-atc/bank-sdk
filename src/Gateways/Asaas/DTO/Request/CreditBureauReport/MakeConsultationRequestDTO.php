<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditBureauReport;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class MakeConsultationRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $customer = null,
        public readonly ?string $cpfCnpj = null,
    ) {}
}