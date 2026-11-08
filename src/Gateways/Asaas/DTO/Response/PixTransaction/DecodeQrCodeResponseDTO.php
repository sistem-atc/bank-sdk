<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\PixTransaction;

use SistemAtc\Banks\Gateways\Asaas\Enums\Finality;
use SistemAtc\Banks\Gateways\Asaas\Enums\AsaasQrCode;
use SistemAtc\Banks\Gateways\Asaas\Enums\OriginPixType;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\Payer;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Receiver;

class DecodeQrCodeResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $payload = null,
        public readonly ?AsaasQrCode $type = null,
        public readonly ?OriginPixType $transactionOriginType = null,
        public readonly ?string $pixKey = null,
        public readonly ?string $conciliationIdentifier = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $expirationDate = null,
        public readonly ?Finality $finality = null,
        public readonly ?float $value = null,
        public readonly ?float $changeValue = null,
        public readonly ?float $interest = null,
        public readonly ?float $fine = null,
        public readonly ?float $discount = null,
        public readonly ?float $totalValue = null,
        public readonly ?bool $canBePaidWithDifferentValue = null,
        public readonly ?bool $canBeModifyChangeValue = null,
        public readonly ?Receiver $receiver = null,
        public readonly ?Payer $payer = null,
        public readonly ?string $description = null,
        public readonly ?bool $canBePaid = null,
        public readonly ?bool $cannotBePaidReason = null,
    ) {}
}