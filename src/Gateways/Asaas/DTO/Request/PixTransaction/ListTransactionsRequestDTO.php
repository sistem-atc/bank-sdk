<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Request\PixTransaction;

use SistemAtc\Banks\Gateways\Asaas\Enums\QrCodeTransaction;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusQrCode;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

class ListTransactionsRequestDTO implements DTOInterface
{
    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?int $offset = 0,
        public readonly ?int $limit = 100,
        public readonly ?StatusQrCode $status = null,
        public readonly ?QrCodeTransaction $type = null,
        public readonly ?string $endToEndIdentifier = null,
    ) {}
}