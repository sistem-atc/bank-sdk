<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListBillPaymentsFilterRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?int $offset = null,
        public readonly ?int $limit = null,
    ) {}
}