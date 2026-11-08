<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Invoice;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class CancelInvoiceRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?bool $cancelOnlyOnAsaas = null,
    ) {}
}