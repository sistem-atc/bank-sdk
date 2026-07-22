<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta dos endpoints resumidos do extrato — calendário de movimentações
 * (`GET /extrato/v1/francesas`) e extrato resumido (`.../movimentacoes-
 * resumidas`). O conteúdo de `data[]` é profundamente aninhado e varia por
 * módulo (cobrança, desconto, tarifação), então é mantido como array cru.
 *
 * @property array<int, mixed> $data
 */
final class ExtratoResumidoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, mixed> $data */
    public function __construct(
        public readonly array $data = [],
    ) {}
}
