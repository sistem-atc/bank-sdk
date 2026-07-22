<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpComprovanteTaxaResumidoResponse.
 */
final class SpComprovanteTaxaItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoSubServico = null,  // ex.: "Alvara-Credenc.Medico e Psicologo"
        public readonly ?string $dataPagamento = null,  // ex.: "15.12.2022"
        public readonly ?int $chavePagamento = null,  // ex.: 202212151543338
        public readonly ?string $horaPagamento = null,  // ex.: "15:43:33"
        public readonly ?int $numeroDocumento = null,  // ex.: 1114030
        public readonly ?int $codigoSubServico = null,  // ex.: 59
        public readonly ?float $valorPagamento = null,  // ex.: 123.08
    ) {}
}
