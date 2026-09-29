<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\Subscription;

/**
 * Webhook de assinatura PagStream. `eventType`: subscription_paused,
 * subscription_reactivated, subscription_canceled,
 * subscription_cycle_skipped, subscription_cycle_unskipped,
 * subscription_frequency_changed, subscription_billing_day_changed,
 * subscription_payment_method_updated.
 *
 * ⚠️ O objeto `subscription` fica FORA da assinatura HMAC (a regra exclui
 * objetos/listas): o que a assinatura garante é o event_type. Pra decidir
 * algo crítico, releia com assinaturas()->consultar().
 */
final class SubscriptionNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $eventType = null,
        public readonly ?Subscription $subscription = null,
        public readonly ?string $signature = null,
    ) {}
}
