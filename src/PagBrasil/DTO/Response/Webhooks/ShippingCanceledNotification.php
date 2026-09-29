<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;

/**
 * Webhook `shipping_canceled` (um por chamada de envios()->alterar() que
 * cancelou). `subscription` é o NÚMERO da assinatura. A lista `shippings`
 * fica fora da assinatura HMAC — releia com envios()->listar() se crítico.
 *
 * @property list<CanceledShipping> $shippings
 */
final class ShippingCanceledNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<CanceledShipping> $shippings */
    public function __construct(
        public readonly ?string $eventType = null,
        public readonly ?string $subscription = null,
        public readonly ?bool $paidCycle = null,
        #[ArrayOf(CanceledShipping::class)]
        public readonly array $shippings = [],
        public readonly ?string $signature = null,
    ) {}
}
