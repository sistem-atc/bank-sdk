<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpServicoResponse.
 */
final class SpServicoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoServico = null,  // ex.: "CNH-CART.NAC.HABILITACAO E REGISTRO"
        public readonly ?int $codigoServico = null,  // ex.: 1
    ) {}
}
