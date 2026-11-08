<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\EscrowAccount;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class EscrowAccountRequestDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly int $daysToExpire,
        public readonly ?bool $enabled = null,
        public readonly ?bool $isFeePayer = null,
    ) {}
}
