<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class ListKeysResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        #[ArrayOf(PixAddressKeyResponseDTO::class)] public readonly ?array $data = null,
    ) {}
}