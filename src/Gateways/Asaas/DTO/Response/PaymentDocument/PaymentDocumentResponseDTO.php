<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\PaymentDocument;

use SistemAtc\Banks\Gateways\Asaas\Enums\DocumentType;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\FileDocument;

final class PaymentDocumentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $object,
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?DocumentType $type,
        public readonly ?bool $availableAfterPayment,
        public readonly ?FileDocument $file,
        public readonly ?bool $deleted,
    ) {}
}