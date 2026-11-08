<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Pix;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\PixAddressKeyType;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\PixAddressKeyStatus;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\QrCode;

final class PixAddressKeyResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $key = null,
        public readonly ?PixAddressKeyType $type = null,
        public readonly ?PixAddressKeyStatus $status = null,
        public readonly ?string $dateCreated = null,
        public readonly ?bool $canBeDeleted = null,
        public readonly ?string $cannotBeDeletedReason = null,
        public readonly ?QrCode $qrCode = null,
    ) {}
}