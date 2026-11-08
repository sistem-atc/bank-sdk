<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Taxes;
use SistemAtc\Banks\Gateways\Asaas\Enums\InvoiceIssuancePeriod;

final class ConfigurationInvoicesRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $municipalServiceId = null,
        public readonly ?string $municipalServiceCode = null,
        public readonly ?string $municipalServiceName = null,
        public readonly ?bool $updatePayment = null,
        public readonly ?float $deductions = null,
        public readonly ?InvoiceIssuancePeriod $effectiveDatePeriod = null,
        public readonly ?bool $receivedOnly = null,
        public readonly ?int $daysBeforeDueDate = null,
        public readonly ?string $observations = null,
        public readonly Taxes $taxes,
    ) {}
}