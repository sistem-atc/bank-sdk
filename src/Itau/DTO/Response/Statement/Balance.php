<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Posição de saldo do Extrato Itaú. Aparece tanto no bloco `balances` de
 * `GET /statements/{statementsId}` quanto em `GET /balances` (e no `summary`
 * consolidado). `type` ∈ {saldo_disponivel, saldo_total, saldo_bloqueado,
 * saldo_aplic_aut, saldo_disponivel_dia, ...}.
 */
final class Balance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?EventDate $date = null,
        public readonly ?Literal $literal = null,
        public readonly ?Amount $amount = null,
    ) {}
}
