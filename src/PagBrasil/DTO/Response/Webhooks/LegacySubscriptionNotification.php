<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\PagBrasil\Enums\SubscriptionStatus;

/**
 * Webhook de assinatura no formato LEGADO (configuração da conta inteira):
 * sem event_type, `subscription` é o número e `status` o código numérico.
 */
final class LegacySubscriptionNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $subscription = null,
        public readonly ?string $amountBrl = null,
        public readonly ?string $status = null,
        public readonly ?string $nextBillingDate = null,
        public readonly ?string $signature = null,
    ) {}

    public function statusEnum(): ?SubscriptionStatus
    {
        return SubscriptionStatus::fromLegacy($this->status);
    }
}
