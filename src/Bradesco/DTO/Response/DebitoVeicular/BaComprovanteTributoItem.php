<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de BaComprovanteDetalhadoResponse.
 */
final class BaComprovanteTributoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoTributo = null,  // ex.: "IPVA"
        public readonly ?float $valorContaTributo = null,  // ex.: 136.85
        public readonly ?string $dataVencimento = null,  // ex.: "00.00.0000"
        public readonly ?string $complementoTributo = null,  // ex.: "000000000002025"
        public readonly ?int $codigoTributo = null,  // ex.: 1
    ) {}
}
