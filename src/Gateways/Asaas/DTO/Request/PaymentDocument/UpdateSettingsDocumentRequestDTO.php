<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDocument;

use SistemAtc\Banks\Gateways\Asaas\Enums\DocumentType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class UpdateSettingsDocumentRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly bool $availableAfterPayment,
        public readonly DocumentType $type,
    ) {}
}
