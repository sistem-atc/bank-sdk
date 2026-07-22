<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Listagem paginada de locations — resposta de `GET /loc`.
 *
 * @property array<int, Location> $loc
 */
final class LocationList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Location> $loc */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(Location::class)]
        public readonly array $loc = [],
    ) {}
}
