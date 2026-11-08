<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\Finance;

use SistemAtc\Banks\Gateways\Asaas\Enums\StatusFinance;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\Enums\BillingTypeEscrow;

final class CollectionStatisticsRequestDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $customer = null,
        public readonly ?BillingTypeEscrow $billingType = null,
        public readonly ?StatusFinance $status = null,
        public readonly ?bool $anticipated = null,
        public readonly ?string $dateCreated__ge = null,
        public readonly ?string $dateCreated__le = null,
        public readonly ?string $dueDate__ge = null,
        public readonly ?string $dueDate__le = null,
        public readonly ?string $estimatedCreditDate__ge = null,
        public readonly ?string $estimatedCreditDate_le = null,
        public readonly ?string $externalReference = null,
    ) {}
}