<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Chargeback;

use SistemAtc\Banks\Gateways\Asaas\Enums\CreditCard;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\ChargebackStatus;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListChargebacksRequestDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 10,
        public readonly ?CreditCard $creditCardBrand = null,
        public readonly ?ChargebackStatus $status = null,
        public readonly ?string $originDisputeDate__le = null,
        public readonly ?string $originDisputeDate__ge = null,
        public readonly ?string $originTransactionDate__le = null,
        public readonly ?string $originTransactionDate__ge = null,
    ) {}
}