<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Consulta paginada de locations de recorrência — resposta de `GET /locrec`
 * (a lista vem na chave `loc`).
 */
final class LocationRecList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Loc> $loc */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(Loc::class)]
        public readonly array $loc = [],
    ) {}
}
