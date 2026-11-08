<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback;

use InvalidArgumentException;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class CreateChargebackDisputeRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        #[MultipartFile(as: 'file')] public readonly string $files,
    ) {
        if (!file_exists($this->files)) {
            throw new InvalidArgumentException("O arquivo não foi encontrado em: {$this->files}");
        }
    }
}
