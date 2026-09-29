<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Enums;

/**
 * Status de assinatura PagStream. A API v2 devolve o rótulo textual; a API v1
 * (legada) e o webhook legado devolvem o código numérico — `fromLegacy()`.
 */
enum SubscriptionStatus: string
{
    case AwaitingFirstPayment = 'awaiting_first_payment';
    case Active = 'active';
    case AwaitingPayment = 'awaiting_payment';
    case Canceled = 'canceled';
    case Expired = 'expired';
    case Paused = 'paused';

    public static function fromLegacy(int|string|null $code): ?self
    {
        return match ((string) $code) {
            '0' => self::AwaitingFirstPayment,
            '1' => self::Active,
            '2' => self::AwaitingPayment,
            '3' => self::Canceled,
            '4' => self::Expired,
            '5' => self::Paused,
            default => null,
        };
    }
}
