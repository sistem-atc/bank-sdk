<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CobsConsultadas` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobsConsultadas`).
 *
 * @property array<int, CobCompleta> $cobs
 */
final class CobsConsultadas implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** [DEPRECADO] Parâmetros utilizados para a realização de uma consulta de cobranças. */
        public readonly ?ParametrosConsultaCob $parametros = null,
        #[ArrayOf(CobCompleta::class)]
        public readonly array $cobs = [],
    ) {}
}
