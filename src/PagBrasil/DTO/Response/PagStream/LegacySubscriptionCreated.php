<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;

/**
 * Resposta de POST /api/pagstream/subscription/add (API v1). `status` é o
 * código NUMÉRICO legado — SubscriptionStatus::fromLegacy().
 */
final class LegacySubscriptionCreated implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $subscription = null,
        public readonly ?string $status = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $nextBillingDate = null,
    ) {}

    public function statusEnum(): ?SubscriptionStatus
    {
        return SubscriptionStatus::fromLegacy($this->status);
    }
}
