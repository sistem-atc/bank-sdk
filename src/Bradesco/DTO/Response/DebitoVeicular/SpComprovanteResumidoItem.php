<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpComprovanteResumidoResponse.
 */
final class SpComprovanteResumidoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomeTributo = null,  // ex.: "IPVA ATUAL"
        public readonly ?int $anoTributo = null,  // ex.: 2025
        public readonly ?string $dataPagamento = null,  // ex.: "12.02.2025"
        public readonly ?int $chavePagamento = null,  // ex.: 101274
        public readonly ?int $codigoTributo = null,  // ex.: 175
        public readonly ?float $valorPagamento = null,  // ex.: 3191.38
        public readonly ?int $nsuBanco = null,  // ex.: 100001538
    ) {}
}
