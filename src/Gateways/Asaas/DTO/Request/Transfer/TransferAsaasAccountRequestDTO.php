<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Transfer;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class TransferAsaasAccountRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly float $value,
        public readonly string $walletId,
        public readonly ?string $externalReference = null,
    ) {}
}