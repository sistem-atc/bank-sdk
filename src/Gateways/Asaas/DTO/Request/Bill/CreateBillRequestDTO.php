<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Bill;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class CreateBillRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly string $identificationField,
        public readonly ?string $scheduleDate = null,
        public readonly ?float $value = null,
        public readonly ?string $description = null,
        public readonly ?float $discount = null,
        public readonly ?float $interest = null,
        public readonly ?float $fine = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $externalReference = null,
    ) {}
}