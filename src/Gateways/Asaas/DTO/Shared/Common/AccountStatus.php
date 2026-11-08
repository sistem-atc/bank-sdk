<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\AccountStatusEnum;

class AccountStatus implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?AccountStatusEnum $commercialInfo = null,
        public readonly ?AccountStatusEnum $bankAccountInfo = null,
        public readonly ?AccountStatusEnum $documentation = null,
        public readonly ?AccountStatusEnum $general = null,
    ) {}
}