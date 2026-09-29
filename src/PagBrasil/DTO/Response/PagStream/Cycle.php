<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Ciclo de cobrança/envio: trio legível por máquina. Padrões de cobrança:
 * W (1 WEEK), M (1 MONTH), Q (1 QUARTER), S (6 MONTH), Y (1 YEAR); ciclos de
 * envio são definidos por loja. `quantity` (envios por ciclo de cobrança) só
 * aparece no snapshot de recorrência.
 */
final class Cycle implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?int $units = null,
        public readonly ?string $interval = null,
        public readonly ?int $quantity = null,
    ) {}
}
