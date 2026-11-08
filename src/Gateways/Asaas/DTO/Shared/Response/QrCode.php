<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Payer;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class QrCode implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?Payer $payer = null,
        public readonly ?string $conciliationIdentifier = null,
        public readonly ?float $originalValue = null,
        public readonly ?string $dueDate = null,
        public readonly ?float $interest = null,
        public readonly ?float $fine = null,
        public readonly ?float $discount = null,
        public readonly ?string $expirationDate = null,
        public readonly ?string $description = null,
    ) {}
}