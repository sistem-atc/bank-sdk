<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

/** Qual WebhookReceiver atende cada banco da rota de webhook. */
final class WebhookReceivers
{
    public static function for(string $bank): ?WebhookReceiver
    {
        return match ($bank) {
            'inter' => new InterWebhookReceiver(),
            default => null,
        };
    }
}
