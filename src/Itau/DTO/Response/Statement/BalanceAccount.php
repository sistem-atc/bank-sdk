<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `data[]` de `GET /balances` — as posições de saldo de UMA conta
 * (`statementId`). Em HTTP 206, a conta com falha vem sem `balances` e com o
 * objeto `error` preenchido.
 *
 * @property list<Balance> $balances
 */
final class BalanceAccount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<Balance> $balances */
    public function __construct(
        public readonly ?string $statementId = null,
        #[ArrayOf(Balance::class)]
        public readonly array $balances = [],
        public readonly ?BalanceError $error = null,
    ) {}
}
