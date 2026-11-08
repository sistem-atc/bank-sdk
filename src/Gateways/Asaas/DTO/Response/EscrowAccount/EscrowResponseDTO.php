<?php

namespace SistemAtc\Banks\Gateways\Asaas\DTO\Response\EscrowAccount;

use SistemAtc\Banks\Gateways\Asaas\Enums\FinishReason;
use SistemAtc\Banks\Gateways\Asaas\Enums\StatusEscrow;
use SistemAtc\Banks\Gateways\Asaas\Traits\AutoHydrate;
use SistemAtc\Banks\Gateways\Asaas\Traits\CastToArray;
use SistemAtc\Banks\Gateways\Asaas\Contracts\DTOInterface;

final class EscrowResponseDTO implements DTOInterface
{

    use CastToArray, AutoHydrate;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?StatusEscrow $status = null,
        public readonly ?string $expirationDate = null,
        public readonly ?string $finishDate = null,
        public readonly ?FinishReason $finishReason = null,
    ) {}
}