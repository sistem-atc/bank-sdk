<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\RecurringPix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\OriginRecurrence;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\FrequencyRecurrence;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusRecurrencesPix;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\RecurrenceExternalAccount;

class SingleRecurrenceResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?StatusRecurrencesPix $status = null,
        public readonly ?OriginRecurrence $origin = null,
        public readonly ?float $value = null,
        public readonly ?FrequencyRecurrence $frequency = null,
        public readonly ?int $quantity = null,
        public readonly ?string $startDate = null,
        public readonly ?string $finishDate = null,
        public readonly ?bool $canBeCancelled = null,
        public readonly ?RecurrenceExternalAccount $externalAccount = null,
    ) {}
}