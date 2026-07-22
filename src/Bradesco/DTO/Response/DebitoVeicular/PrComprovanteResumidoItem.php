<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de PrComprovanteResumidoResponse.
 */
final class PrComprovanteResumidoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "COTA 1"
        public readonly ?string $dataPagamento = null,  // ex.: "18/08/2023"
        public readonly ?float $valorContaTributo = null,  // ex.: 200.1
        public readonly ?string $dataVencimento = null,  // ex.: "13/12/2023"
        public readonly ?int $dataHoraPagamento = null,  // ex.: 202308180913300
        public readonly ?int $codigoTributo = null,  // ex.: 451
        public readonly ?int $anoExercicio = null,  // ex.: 2023
    ) {}
}
