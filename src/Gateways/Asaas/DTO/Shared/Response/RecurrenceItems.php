<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusRecurrencesItems;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class RecurrenceItems implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?StatusRecurrencesItems $status = null,
        public readonly ?string $scheduledDate = null,
        public readonly ?bool $canBeCancelled = null,
        public readonly ?int $recurrenceNumber = null,
        public readonly ?int $quantity = null,
        public readonly ?float $value = null,
        public readonly ?string $refusalReasonDescription = null,
        public readonly ?RecurrenceExternalAccount $externalAccount = null,
    ) {}
}