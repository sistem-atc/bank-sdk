<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\MobilePhoneRecharge;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusMobile;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class RechargeResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?float $value = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?StatusMobile $status = null,
        public readonly ?bool $canBeCancelled = null,
        public readonly ?string $operatorName = null,
    ) {}
}