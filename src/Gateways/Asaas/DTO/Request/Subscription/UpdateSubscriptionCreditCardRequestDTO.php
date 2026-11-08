<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\CreditCard;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\CreditCardHolderInfo;

final class UpdateSubscriptionCreditCardRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly CreditCard $creditCard,
        public readonly CreditCardHolderInfo $creditCardHolderInfo,
        public readonly ?string $creditCardToken = null,
        public readonly string $remoteIp,
    ) {}
}