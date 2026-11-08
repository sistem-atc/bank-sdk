<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response;

use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\TedFees;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\PixTransferFees;

class TransferFees implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $monthlyTransfersWithoutFee = null,
        public readonly ?TedFees $ted = null,
        public readonly ?PixTransferFees $pix = null,
    ) {}
}