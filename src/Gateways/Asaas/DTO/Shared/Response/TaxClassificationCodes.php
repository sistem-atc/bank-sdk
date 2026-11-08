<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class TaxClassificationCodes implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $description = null,
        public readonly ?string $effectiveStartDate = null,
        public readonly ?string $expirationDate = null,
        public readonly ?bool $isSubjectRegularTaxation = null,
        public readonly ?float $cbsPercentage = null,
        public readonly ?float $municipalIbsTaxPercentage = null,
        public readonly ?float $stateIbsTaxPercentage = null,
        public readonly ?float $cbsTaxReductionPercentage = null,
        public readonly ?float $ibsTaxReductionPercentage = null,
        public readonly ?string $taxRegimeType = null,
        public readonly ?TaxSituation $taxSituation = null,
    ) {}
}
