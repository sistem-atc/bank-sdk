<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AutomaticPix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusPixPayment;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Authorization;

final class SinglePaymentResponseDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?bool $endToEndIdentifier = null,
        public readonly ?Authorization $authorization = null,
        public readonly ?string $dueDate = null,
        public readonly ?StatusPixPayment $status = null,
        public readonly ?string $paymentId = null,
    ) {}
}