<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation;

use InvalidArgumentException;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class RequestAnticipationRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        public readonly ?string $installment = null,
        public readonly ?string $payment = null,
        #[MultipartFile(as: 'documents')] public readonly ?string $documentFilePath = null,
    ) {
        if ($this->documentFilePath && !file_exists($this->documentFilePath)) {
            throw new InvalidArgumentException("O documento para antecipação não foi encontrado em: {$this->documentFilePath}");
        }
    }
}