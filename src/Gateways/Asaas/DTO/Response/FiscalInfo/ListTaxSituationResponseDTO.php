<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\FiscalInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\TaxSituation;

final class ListTaxSituationResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?bool $object = null,
        public readonly ?bool $hasMore = null,
        public readonly ?int $totalCount = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        #[ArrayOf(TaxSituation::class)] public readonly ?array $data = null,
    ) {}
}