<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Saldo com a data do pregao de referencia.
 *
 * Origem: components.schemas.ResponseContent (managers-balance-check).
 */
final class ConteudoSaldo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data do pregao a que o saldo se refere. */
        public readonly ?string $tradingSessionDate = null,
        /** Valor do saldo. */
        public readonly ?float $balance = null,
    ) {}
}
