<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Sispag;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada de `GET /sispag/v1/pagamentos_sispag`. O corpo real vem
 * aninhado em `data` ({itens, total, pagination}); o método do Endpoint
 * desembrulha o `data` antes de hidratar.
 *
 * @property list<PagamentoSispagItem> $itens
 */
final class PagamentosSispagList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<PagamentoSispagItem> $itens */
    public function __construct(
        #[ArrayOf(PagamentoSispagItem::class)]
        public readonly array $itens = [],
        public readonly ?string $total = null,
        public readonly ?int $page = null,
        public readonly ?int $pageSize = null,
    ) {}
}
