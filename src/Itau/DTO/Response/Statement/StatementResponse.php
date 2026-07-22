<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta de `GET /statements/{statementsId}` — o extrato paginado da conta no
 * período. `data` traz os blocos de lançamentos/saldos e `pagination` os links
 * e contadores. Em HTTP 206 o extrato/saldo vêm normais e os `pending_events`
 * de cada bloco carregam a mensagem de erro.
 *
 * @property list<StatementData> $data
 */
final class StatementResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<StatementData> $data */
    public function __construct(
        #[ArrayOf(StatementData::class)]
        public readonly array $data = [],
        public readonly ?Pagination $pagination = null,
    ) {}
}
