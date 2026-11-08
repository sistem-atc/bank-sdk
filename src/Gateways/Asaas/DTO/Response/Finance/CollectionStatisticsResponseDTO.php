<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Finance;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class CollectionStatisticsResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $quantity = null,
        public readonly ?float $value = null,
        public readonly ?float $netValue = null,
    ) {}
}