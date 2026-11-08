<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Installment;

use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\SplitRefunds;

final class RefundInstallmentRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?float $value = null,
        #[ArrayOf(SplitRefunds::class)] public readonly ?array $splitRefunds = null,
    ) {}
}