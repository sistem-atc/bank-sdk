<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Conciliacao;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada de `GET /conciliacao/v1/lancamentos-pix` — a lista de
 * lançamentos vem embrulhada em `data`.
 *
 * @property list<LancamentoPix> $itens
 */
final class LancamentosPixList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<LancamentoPix> $itens */
    public function __construct(
        #[ArrayOf(LancamentoPix::class)]
        public readonly array $itens = [],
        public readonly ?int $page = null,
        public readonly ?int $pageSize = null,
    ) {}
}
