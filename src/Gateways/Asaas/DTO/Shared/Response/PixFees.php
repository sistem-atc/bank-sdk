<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class PixFees implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?float $fixedFeeValue = null,
        public readonly ?float $fixedFeeValueWithDiscount = null,
        public readonly ?float $percentageFee = null,
        public readonly ?float $minimumFeeValue = null,
        public readonly ?float $maximumFeeValue = null,
        public readonly ?string $discountExpiration = null,
        public readonly ?int $monthlyCreditsWithoutFee = null,
        public readonly ?int $creditsReceivedOfCurrentMonth = null,
    ) {}
}