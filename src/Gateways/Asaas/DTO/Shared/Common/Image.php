<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class Image implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $originalName = null,
        public readonly ?int $size = null,
        public readonly ?string $extension = null,
        public readonly ?string $previewUrl = null,
        public readonly ?string $downloadUrl = null,
    ) {}
}