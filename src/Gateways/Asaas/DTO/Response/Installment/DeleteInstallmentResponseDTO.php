<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Installment;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class DeleteInstallmentResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?bool $deleted = null,
        public readonly ?string $id = null,
    ) {}
}