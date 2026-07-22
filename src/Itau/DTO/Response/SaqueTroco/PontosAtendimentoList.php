<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\SaqueTroco;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada de `GET /saque-troco/v1/pontos-atendimento` (parâmetros
 * opcionais `pontoAtendimentoId`, `page`, `pageSize`).
 *
 * @property list<PontoAtendimento> $itens
 */
final class PontosAtendimentoList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<PontoAtendimento> $itens */
    public function __construct(
        #[ArrayOf(PontoAtendimento::class)]
        public readonly array $itens = [],
        public readonly ?string $total = null,
        public readonly ?int $page = null,
        public readonly ?int $pageSize = null,
    ) {}
}
