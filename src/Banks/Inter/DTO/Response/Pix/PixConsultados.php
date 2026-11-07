<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PixConsultados` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixConsultados`).
 *
 * @property array<int, Pix> $pix
 */
final class PixConsultados implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Parâmetros utilizados para a realização de uma consulta de Pix. */
        public readonly ?ParametrosConsultaPix $parametros = null,
        #[ArrayOf(Pix::class)]
        public readonly array $pix = [],
    ) {}
}
