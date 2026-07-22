<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `date` do Extrato Itaú (Account Statement). `event` é a data/hora do
 * evento (ISO 8601 com offset, ex.: "2024-04-25T02:59:00Z"); `accounting` é a
 * data contábil ("yyyy-MM-dd"). Saldos trazem apenas `event`.
 */
final class EventDate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $event = null,
        public readonly ?string $accounting = null,
    ) {}
}
