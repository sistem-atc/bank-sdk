<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class DeleteQrCodeStaticResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?bool $deleted = null,
    ) {}
}