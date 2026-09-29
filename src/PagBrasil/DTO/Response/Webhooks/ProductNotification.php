<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;

/**
 * Webhook de produto do catálogo de assinatura (ligar/desligar em
 * produtos()->configurar()). Ligado: sku + regra padrão (`frequency` = código
 * do ciclo, `unit` = unidades, `billingCycle` = intervalo, `orderTrigger`).
 * Desligado: `action` = "delete" + sku.
 */
final class ProductNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sku = null,
        public readonly ?string $action = null,
        public readonly ?string $frequency = null,
        public readonly ?int $unit = null,
        public readonly ?string $billingCycle = null,
        public readonly ?bool $orderTrigger = null,
        public readonly ?string $signature = null,
    ) {}

    public function isDisabled(): bool
    {
        return $this->action === 'delete';
    }
}
