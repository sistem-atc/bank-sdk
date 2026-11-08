<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PaymentLink;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListPaymentLinkRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?bool $active = null,
        public readonly ?bool $includeDeleted = null,
        public readonly ?string $name = null,
        public readonly ?string $externalReference = null,
    ) {}
}
