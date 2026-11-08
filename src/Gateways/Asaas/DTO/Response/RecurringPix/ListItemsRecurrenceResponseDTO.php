<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\RecurringPix;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\RecurrenceItems;

class ListItemsRecurrenceResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        #[ArrayOf(RecurrenceItems::class)] public readonly ?array $data = null,
    ) {}
}