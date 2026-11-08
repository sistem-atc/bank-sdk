<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentSplit;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusSplit;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListSplitsRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?string $paymentId = null,
        public readonly ?StatusSplit $status = null,
        public readonly ?string $paymentConfirmedDate__ge = null,
        public readonly ?string $paymentConfirmedDate__le = null,
        public readonly ?string $creditDate__ge = null,
        public readonly ?string $creditDate__le = null,
    ) {}
}
