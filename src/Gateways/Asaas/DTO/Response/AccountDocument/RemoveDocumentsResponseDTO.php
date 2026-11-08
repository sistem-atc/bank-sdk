<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class RemoveDocumentsResponseDTO implements DTOInterface
{
    
    use CastToArray, AutoHydrate;

    public function __construct(
        public ?bool $deleted = null,
        public ?string $id = null,
    ) {}
}