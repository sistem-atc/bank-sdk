<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class DeleteConfigurationResponseDTO implements DTOInterface
{
    use AutoHydrate, CastToArray;

    public function __construct(
        public readonly ?bool $deleted = null,
        public readonly ?string $id = null,
    ) {}
}