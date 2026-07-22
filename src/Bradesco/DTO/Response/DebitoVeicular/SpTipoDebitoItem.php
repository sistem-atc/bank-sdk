<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpTipoDebitoResponse.
 */
final class SpTipoDebitoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoTributo = null,  // ex.: "IPVA ANTERIORES"
        public readonly ?int $codigoTributo = null,  // ex.: 2
    ) {}
}
