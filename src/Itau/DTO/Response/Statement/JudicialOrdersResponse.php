<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta de `GET /statements/{statementsId}/judicial-orders` — as ordens
 * judiciais de bloqueio da conta no período, paginadas.
 *
 * @property list<JudicialOrder> $data
 */
final class JudicialOrdersResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<JudicialOrder> $data */
    public function __construct(
        #[ArrayOf(JudicialOrder::class)]
        public readonly array $data = [],
        public readonly ?Pagination $pagination = null,
    ) {}
}
