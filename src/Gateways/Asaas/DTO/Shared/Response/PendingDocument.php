<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusDocument;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\TypePendingDocument;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Documents;

class PendingDocument implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?StatusDocument $status = null,
        public readonly ?TypePendingDocument $type = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?Responsible $responsible = null,
        public readonly ?string $onboardingUrl = null,
        public readonly ?string $onboardingUrlExpirationDate = null,
        public readonly ?Documents $documents = null,
    ) {}
}