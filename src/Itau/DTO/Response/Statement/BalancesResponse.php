<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta de `GET /balances` — a posição de saldo (rápida/performática) de
 * todas as contas da integração. `data` tem uma entrada por conta e `summary`
 * o consolidado por tipo de saldo (vazio em HTTP 206).
 *
 * @property list<BalanceAccount> $data
 * @property list<Balance> $summary
 */
final class BalancesResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param list<BalanceAccount> $data
     * @param list<Balance> $summary
     */
    public function __construct(
        #[ArrayOf(BalanceAccount::class)]
        public readonly array $data = [],
        #[ArrayOf(Balance::class)]
        public readonly array $summary = [],
    ) {}
}
