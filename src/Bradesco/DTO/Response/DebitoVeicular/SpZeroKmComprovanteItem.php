<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpZeroKmComprovanteResumidoResponse.
 */
final class SpZeroKmComprovanteItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoTributo = null,  // ex.: "VEICULOS NOVOS"
        public readonly ?string $dataPagamento = null,  // ex.: "11.01.2023"
        public readonly ?int $chavePagamento = null,  // ex.: 202301111524168
        public readonly ?string $horaPagamento = null,  // ex.: "15:24:16"
        public readonly ?int $codigoTributo = null,  // ex.: 62
        public readonly ?float $valorPagamento = null,  // ex.: 419.03
    ) {}
}
