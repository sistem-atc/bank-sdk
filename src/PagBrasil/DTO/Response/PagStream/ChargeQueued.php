<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Cobrança imediata aceita (HTTP 202) — assíncrona. O desfecho sai no
 * histórico de tentativas da recorrência. `queuedAt` é ISO-8601 com offset.
 */
final class ChargeQueued implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $subscriptionNumber = null,
        public readonly ?int $recurrenceNumber = null,
        public readonly ?string $status = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $queuedAt = null,
    ) {}
}
