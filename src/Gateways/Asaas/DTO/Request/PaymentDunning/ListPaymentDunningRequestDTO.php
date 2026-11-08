<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentDunning;

use SistemAtc\Banks\Gateways\Asaas\Enums\DunningType;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusDunning;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListPaymentDunningRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?StatusDunning $status,
        public readonly ?DunningType $type,
        public readonly ?string $payment,
        public readonly ?string $requestStartDate,
        public readonly ?string $requestEndDate,
    ) {}
}
