<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Payment;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ViewInformationResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $invoiceViewedDate = null,
        public readonly ?string $boletoViewedDate = null,
    ) {}
}