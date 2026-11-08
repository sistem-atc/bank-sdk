<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Subscription;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Request\Taxes;
use SistemAtc\Banks\Gateways\Asaas\Enums\InvoiceIssuancePeriod;

final class UpdateConfigurationInvoicesRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?float $deductions = null,
        public readonly ?InvoiceIssuancePeriod $effectiveDatePeriod = null,
        public readonly ?bool $receivedOnly = null,
        public readonly ?int $daysBeforeDueDate = null,
        public readonly ?string $observations = null,
        public readonly Taxes $taxes,
    ) {}
}