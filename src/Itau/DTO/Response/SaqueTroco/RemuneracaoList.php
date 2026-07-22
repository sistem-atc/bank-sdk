<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\SaqueTroco;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada das consultas de remuneração de Saque Pix
 * (`GET /remuneracao-analiticos` e `GET /remuneracao-consolidados`), por conta
 * e período (`dataLancamento` = "YYYY-MM-DD,YYYY-MM-DD").
 *
 * @property list<RemuneracaoItem> $itens
 */
final class RemuneracaoList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<RemuneracaoItem> $itens */
    public function __construct(
        #[ArrayOf(RemuneracaoItem::class)]
        public readonly array $itens = [],
        public readonly ?string $total = null,
        public readonly ?int $page = null,
        public readonly ?int $pageSize = null,
    ) {}
}
