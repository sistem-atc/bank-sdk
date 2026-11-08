<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class ResendDocumentRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        #[MultipartFile(as: 'documents')] public readonly string $documents,
    ) {}
}