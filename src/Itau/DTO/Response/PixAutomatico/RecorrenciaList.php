<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Consulta paginada de recorrências — resposta de `GET /rec`.
 */
final class RecorrenciaList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Recorrencia> $recs */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(Recorrencia::class)]
        public readonly array $recs = [],
    ) {}
}
