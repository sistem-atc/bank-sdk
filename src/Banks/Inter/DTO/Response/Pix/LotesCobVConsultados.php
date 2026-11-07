<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `LotesCobVConsultados` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `LotesCobVConsultados`).
 *
 * @property array<int, LoteCobVConsultado> $lotes
 */
final class LotesCobVConsultados implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Parâmetros utilizados para a realização de uma consulta de lote de cobranças com vencimento. */
        public readonly ?ParametrosConsultaLote $parametros = null,
        #[ArrayOf(LoteCobVConsultado::class)]
        public readonly array $lotes = [],
    ) {}
}
