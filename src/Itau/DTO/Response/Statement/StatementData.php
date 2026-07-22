<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco de `data[]` de `GET /statements/{statementsId}` — agrupa os lançamentos
 * (`events`), as posições de saldo (`balances`) e, quando pedido, os lançamentos
 * pendentes (`pending_events`) da conta no período consultado.
 *
 * @property list<StatementEvent> $events
 * @property list<Balance> $balances
 * @property list<PendingEvent> $pendingEvents
 */
final class StatementData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param list<StatementEvent> $events
     * @param list<Balance> $balances
     * @param list<PendingEvent> $pendingEvents
     */
    public function __construct(
        #[ArrayOf(StatementEvent::class)]
        public readonly array $events = [],
        #[ArrayOf(Balance::class)]
        public readonly array $balances = [],
        #[ArrayOf(PendingEvent::class)]
        public readonly array $pendingEvents = [],
    ) {}
}
