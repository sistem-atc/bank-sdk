<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class TaxSituation implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $description = null,
        public readonly ?bool $isSubjectToIbsCbsTaxation = null,
        public readonly ?bool $isBaseReductionPercentApplicable = null,
        public readonly ?bool $isDefermentApplicable = null,
    ) {}
}
