<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `devolucoes.horario` — momentos da devolução no PSP. `liquidacao` só
 * aparece após a efetivação. Timestamps mantidos como string (ISO).
 */
final class HorarioDevolucao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $solicitacao = null,
        public readonly ?string $liquidacao = null,
    ) {}
}
