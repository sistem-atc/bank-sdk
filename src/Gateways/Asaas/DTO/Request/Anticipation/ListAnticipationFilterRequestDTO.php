<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Anticipation;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusReceivable;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListAnticipationFilterRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?int $offset = null,
        public readonly ?int $limit = null,
        public readonly ?string $payment = null,
        public readonly ?string $installment = null,
        public readonly ?StatusReceivable $status = null,
    ) {}
}