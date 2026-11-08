<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\FiscalInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListTaxClassificationRequestDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?string $code = null,
        public readonly ?string $description = null,
        public readonly ?string $taxSituationCode = null,
    ) {}
}