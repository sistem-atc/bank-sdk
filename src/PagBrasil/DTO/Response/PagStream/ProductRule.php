<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Regra de ciclo de um produto do catálogo. `limit` 0 = sem limite;
 * `cycleTurnoverDay` 0 = desligado. Regra nunca é apagada — só inativada.
 */
final class ProductRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?Cycle $billingCycle = null,
        public readonly ?Cycle $shippingCycle = null,
        public readonly ?string $status = null,
        public readonly ?int $limit = null,
        public readonly ?int $billingDay = null,
        public readonly ?int $cycleTurnoverDay = null,
        public readonly ?bool $isDefault = null,
        public readonly ?bool $inUseBySubscriptions = null,
    ) {}
}
