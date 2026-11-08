<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\CreditBureauReport;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class MakeConsultationResponseDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $cpfCnpj = null,
        public readonly ?string $customer = null,
        public readonly ?string $downloadUrl = null,
        public readonly ?string $reportFile = null,
    ) {}
}