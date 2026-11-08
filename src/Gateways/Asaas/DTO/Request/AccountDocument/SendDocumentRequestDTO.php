<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountDocument;

use InvalidArgumentException;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Enums\TypePendingDocument;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class SendDocumentRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        #[MultipartFile(as: 'documentFile')] public readonly string $filePath,
        public readonly ?TypePendingDocument $type = null,
    ) {
        if (!file_exists($this->filePath)) {
            throw new InvalidArgumentException("O arquivo não foi encontrado em: {$this->filePath}");
        }
    }
}
