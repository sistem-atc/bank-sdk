<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada do extrato detalhado de movimentações — `GET /extrato/v1/
 * francesas/{francesaId}/movimentacoes`. Movimentações em `data[]`, paginação
 * em `pagination`.
 *
 * @property list<MovimentacaoBoleto> $data
 */
final class MovimentacaoExtratoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<MovimentacaoBoleto> $data */
    public function __construct(
        #[ArrayOf(MovimentacaoBoleto::class)]
        public readonly array $data = [],
        /** @var array<string, mixed>|null */
        public readonly ?array $pagination = null,
    ) {}
}
