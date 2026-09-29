<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Tentativa de cobrança (histórico da recorrência e GET /charges).
 * `outcome`: pending, success, error. `orderStatus`/`errorCode` só em
 * cartão (código do adquirente). `subscriptionCode`/`orderNumber` só em
 * GET /charges.
 */
final class ChargeAttempt implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $attemptId = null,
        public readonly ?int $attemptNumber = null,
        public readonly ?string $outcome = null,
        public readonly ?string $type = null,
        public readonly ?bool $attempted = null,
        public readonly ?string $scheduledFor = null,
        public readonly ?string $attemptedAt = null,
        public readonly ?string $subscriptionCode = null,
        public readonly ?string $orderNumber = null,
        public readonly ?string $orderStatus = null,
        public readonly ?string $errorCode = null,
    ) {}
}
