<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class PixTransferFees implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?float $feeValue = null,
        public readonly ?float $discountValue = null,
        public readonly ?string $expirationDate = null,
        public readonly ?bool $consideredInMonthlyTransfersWithoutFee = null,
    ) {}
}