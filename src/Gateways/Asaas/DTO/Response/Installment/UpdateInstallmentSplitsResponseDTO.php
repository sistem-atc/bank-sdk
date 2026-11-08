<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Split;

final class UpdateInstallmentSplitsResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        #[ArrayOf(Split::class)] public readonly ?array $splits = null,
    ) {}
}