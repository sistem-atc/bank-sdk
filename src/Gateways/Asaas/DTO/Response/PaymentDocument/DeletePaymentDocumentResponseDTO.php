<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDocument;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class DeletePaymentDocumentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?bool $deleted,
        public readonly ?string $id,
    ) {}
}