<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountDocument;

use SistemAtc\Banks\Gateways\Asaas\Attributes\ArrayOf;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PendingDocument;

final class CheckPendingDocumentsResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;
    
    public function __construct(
        public readonly ?string $rejectReasons = null,
        #[ArrayOf(PendingDocument::class)] readonly ?array $data = null,
    ) {}
}