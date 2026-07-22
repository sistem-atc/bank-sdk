<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpListaDebitosResponse.
 */
final class SpDebitoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "IPVA"
        public readonly ?string $autoInfracaoMulta = null,
        public readonly ?string $descricaoEnquadramentoMulta = null,
        public readonly ?string $indicadorPagamentoTributo = null,  // ex.: "N"
        public readonly ?int $codigoEnquadramentoMulta = null,  // ex.: 0
        public readonly ?int $codigoPesoMulta = null,  // ex.: 0
        public readonly ?string $nomeOrgaoMulta = null,
        public readonly ?int $numeroGuiaMulta = null,  // ex.: 0
        public readonly ?string $descricaoPesoMulta = null,
        public readonly ?string $anoTributo = null,  // ex.: "2022"
        public readonly ?string $dataInfracaoMulta = null,
        public readonly ?string $descricaoTributo = null,
        public readonly ?float $valorTributo = null,  // ex.: 1017.42
        public readonly ?string $dataVencimentoMulta = null,
        public readonly ?int $codigoTributo = null,  // ex.: 1
        public readonly ?int $codigoOrgaoMulta = null,  // ex.: 0
    ) {}
}
