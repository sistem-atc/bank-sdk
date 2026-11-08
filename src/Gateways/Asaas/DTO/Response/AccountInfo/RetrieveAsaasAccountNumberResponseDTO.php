<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class RetrieveAsaasAccountNumberResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $agency = null,
        public readonly ?string $account = null,
        public readonly ?string $accountDigit = null,
    ) {}
}