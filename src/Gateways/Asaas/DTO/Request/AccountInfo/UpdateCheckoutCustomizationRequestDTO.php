<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo;

use InvalidArgumentException;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class UpdateCheckoutCustomizationRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;

    public function __construct(
        public readonly string $logoBackgroundColor,
        public readonly string $infoBackgroundColor,
        public readonly string $fontColor,
        public readonly ?bool $enabled = null,
        #[MultipartFile(as: 'logoFile')] public readonly ?string $logoFilePath = null,
    ) {
        if ($this->logoFilePath && !file_exists($this->logoFilePath)) {
            throw new InvalidArgumentException("O arquivo de logo não foi encontrado em: {$this->logoFilePath}");
        }
    }
}