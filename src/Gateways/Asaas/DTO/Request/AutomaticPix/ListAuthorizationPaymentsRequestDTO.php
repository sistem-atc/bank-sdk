<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPixPayment;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListAuthorizationPaymentsRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $authorizationId = null,
        public readonly ?string $customerId = null,
        public readonly ?string $paymentId = null,
        public readonly ?StatusPixPayment $status = null,
    ) {}
}