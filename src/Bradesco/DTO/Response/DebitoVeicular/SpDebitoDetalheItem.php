<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `listaDebito` de SpComprovanteRenavamResponse.
 */
final class SpDebitoDetalheItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $codigoTributoDebito = null,
        public readonly ?string $dataVencimentoDebito = null,
        public readonly ?string $descricaoTributoDebito = null,
        public readonly ?float $valorDebito = null,
        public readonly ?int $anoExercicioDebito = null,
    ) {}
}
