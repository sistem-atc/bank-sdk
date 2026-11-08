<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class DeleteWhiteLabelSubaccountResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $observations = null,
    ) {}
}