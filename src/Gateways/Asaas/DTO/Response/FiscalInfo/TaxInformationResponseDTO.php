<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\FiscalInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class TaxInformationResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $email = null,
        public readonly ?string $municipalInscription = null,
        public readonly ?bool $simplesNacional = null,
        public readonly ?bool $culturalProjectsPromoter = null,
        public readonly ?string $cnae = null,
        public readonly ?string $specialTaxRegime = null,
        public readonly ?string $serviceListItem = null,
        public readonly ?string $nbsCode = null,
        public readonly ?string $rpsSerie = null,
        public readonly ?int $rpsNumber = null,
        public readonly ?int $loteNumber = null,
        public readonly ?string $username = null,
        public readonly ?bool $passwordSent = null,
        public readonly ?bool $accessTokenSent = null,
        public readonly ?bool $certificateSent = null,
        public readonly ?string $nationalPortalTaxCalculationRegime = null,
    ) {}
}