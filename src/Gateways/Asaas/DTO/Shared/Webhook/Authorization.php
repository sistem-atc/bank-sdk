<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Webhook;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\ImmediateQrCode;

class Authorization implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $status = null,
        public readonly ?string $customerId = null,
        public readonly ?string $frequency = null,
        public readonly ?float $value = null,
        public readonly ?string $startDate = null,
        public readonly ?string $finishDate = null,
        public readonly ?ImmediateQrCode $immediateQrCode = null,
    ) {}
}