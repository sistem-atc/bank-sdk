<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\RecurringPix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusRecurrencesPix;

class ListRecurrencesRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?StatusRecurrencesPix $status = null,
        public readonly ?float $value = null,
        public readonly ?string $searchText = null,
    ) {}
}