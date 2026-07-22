<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de BaTiposDebitosResponse.
 */
final class BaTipoDebitoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoDebito = null,  // ex.: "LICENCIAMENTO COTA UNICA ATUAL"
        public readonly ?int $codigoDebito = null,  // ex.: 401
    ) {}
}
