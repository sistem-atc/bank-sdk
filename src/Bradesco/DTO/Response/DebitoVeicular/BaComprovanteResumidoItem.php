<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de BaComprovanteResumidoResponse.
 */
final class BaComprovanteResumidoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoTributo = null,  // ex.: "LICENCIAMENTO PARCELADO"
        public readonly ?string $dataPagamento = null,  // ex.: "29.04.2025"
        public readonly ?float $valorContaTributo = null,  // ex.: 136.85
        public readonly ?int $origemPagamento = null,  // ex.: 0
        public readonly ?int $codigoPagamento = null,  // ex.: 403
        public readonly ?string $anoExercicio = null,  // ex.: "2025"
        public readonly ?int $codigoAgencia = null,  // ex.: 145
        public readonly ?int $nsuBanco = null,  // ex.: 26592
    ) {}
}
