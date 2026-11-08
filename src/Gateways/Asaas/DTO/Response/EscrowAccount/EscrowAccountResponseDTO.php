<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\EscrowAccount;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class EscrowAccountResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $daysToExpire = null,
        public readonly ?bool $enabled = null,
        public readonly ?bool $isFeePayer = null,
    ) {}
}