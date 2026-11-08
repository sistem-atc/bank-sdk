<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDocument;

use InvalidArgumentException;
use SistemAtc\Banks\Gateways\Asaas\Enums\DocumentType;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class UploadPaymentDocumentRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        public readonly bool $availableAfterPayment,
        public readonly DocumentType $type,
        #[MultipartFile(as: 'file')] public readonly string $file,
    ) {
        if (!file_exists($this->file)) {
            throw new InvalidArgumentException("O arquivo não foi encontrado em: {$this->file}");
        }
    }
}