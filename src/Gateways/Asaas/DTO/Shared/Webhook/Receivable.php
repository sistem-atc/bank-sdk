<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusReceivable;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Receivable implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $object = null,
        public readonly ?string $id = null,
        public readonly ?string $installment = null,
        public readonly ?string $payment = null,
        public readonly ?StatusReceivable $status = null,
        public readonly ?string $anticipationDate = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $requestDate = null,
        public readonly ?float $fee = null,
        public readonly ?int $anticipationDays = null,
        public readonly ?float $netValue = null,
        public readonly ?float $totalValue = null,
        public readonly ?float $value = null,
        public readonly ?string $denialObservation = null,
    ) {}
}