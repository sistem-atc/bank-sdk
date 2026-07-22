<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Par valor/limite do saldo de margem.
 *
 * Origem: components.schemas.BalanceResponse.
 */
final class SaldoValorLimite implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Valor utilizado. */
        public readonly ?float $value = null,
        /** Limite contratado. */
        public readonly ?float $limit = null,
    ) {}
}
