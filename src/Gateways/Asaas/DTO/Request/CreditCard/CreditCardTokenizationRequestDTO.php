<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\CreditCard;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\CreditCard;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\CreditCardHolderInfo;

final class CreditCardTokenizationRequestDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly string $customer,
        public readonly CreditCard $creditCard,
        public readonly CreditCardHolderInfo $creditCardHolderInfo,
        public readonly string $remoteIp,
    ) {}
}