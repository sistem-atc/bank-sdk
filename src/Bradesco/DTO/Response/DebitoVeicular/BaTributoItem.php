<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de BaListaDebitosResponse.
 */
final class BaTributoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "TAXA DE LICENCIAMENTO"
        public readonly ?string $descricaoTributo = null,  // ex.: "2025"
        public readonly ?float $valorContaTributo = null,  // ex.: 173.4
        public readonly ?string $dataVencimento = null,  // ex.: "00.00.0000"
        public readonly ?int $codigoTributo = null,  // ex.: 3
    ) {}
}
