<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de PrListaDebitosResponse.
 */
final class PrDebitoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "COTA UNICA"
        public readonly ?string $descricaoTributo = null,  // ex.: "13/12/2024"
        public readonly ?float $valorContaTributo = null,  // ex.: 1190.0
        public readonly ?int $codigoTributo = null,  // ex.: 451
        public readonly ?int $anoExercicio = null,  // ex.: 2023
    ) {}
}
