<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Opção de troca de ciclo. Mande `optionKey` de volta EXATAMENTE como veio
 * (formato "<cobrança>_<envio>", ex.: "M_M4W", "W_"). `limit` 0 = sem limite.
 */
final class BillingCycleOption implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $optionKey = null,
        public readonly ?Cycle $billingCycle = null,
        public readonly ?Cycle $shippingCycle = null,
        public readonly ?int $limit = null,
        public readonly ?int $billingDay = null,
    ) {}
}
