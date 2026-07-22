<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `amount` do Extrato Itaú (Account Statement) — valor monetário de um
 * lançamento, saldo ou evento pendente. `value` vem numérico (ex.: 500.00) e
 * `currency` no padrão ISO ("BRL").
 */
final class Amount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $value = null,
        public readonly ?string $currency = null,
    ) {}
}
