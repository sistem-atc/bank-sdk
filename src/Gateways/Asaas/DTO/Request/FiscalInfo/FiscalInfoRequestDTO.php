<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToMultipart;
use SistemAtc\Banks\Gateways\Asaas\Attributes\MultipartFile;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterfaceMultipart;

final class FiscalInfoRequestDTO implements DTOInterfaceMultipart
{
    use CastToMultipart;
    
    public function __construct(
        public readonly string $email,
        public readonly ?string $municipalInscription = null,
        public readonly bool $simplesNacional = true,
        public readonly ?bool $culturalProjectsPromoter = null,
        public readonly ?string $cnae = null,
        public readonly ?string $specialTaxRegime = null,
        public readonly ?string $serviceListItem = null,
        public readonly ?string $nbsCode = null,
        public readonly ?string $rpsSerie = null,
        public readonly ?int $rpsNumber = null,
        public readonly ?int $loteNumber = null,
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?string $accessToken = null,
        #[MultipartFile(as: 'certificateFile')] public readonly ?string $certificateFile = null,
        public readonly ?string $certificatePassword = null,
        public readonly ?string $nationalPortalTaxCalculationRegime = null,
    ) {}
}