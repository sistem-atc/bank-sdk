<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `listaMulta` de SpComprovanteRenavamResponse.
 */
final class SpMultaItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dataInfracaoMulta = null,
        public readonly ?string $descricaoOrgaoMulta = null,
        public readonly ?int $codigoTributoMulta = null,
        public readonly ?string $descricaoTributoMulta = null,
        public readonly ?string $dataVencimentoMulta = null,
        public readonly ?int $codigoOrigemMulta = null,
        public readonly ?int $codigoMunicipioMulta = null,
        public readonly ?float $valorMulta = null,
        public readonly ?string $numeroGuiaMulta = null,
    ) {}
}
