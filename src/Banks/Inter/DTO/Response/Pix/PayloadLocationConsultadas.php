<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PayloadLocationConsultadas` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PayloadLocationConsultadas`).
 *
 * @property array<int, PayloadLocationCompleta> $loc
 */
final class PayloadLocationConsultadas implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Parâmetros utilizados para a realização de uma consulta de locations. */
        public readonly ?ParametrosConsultaPayloadLocation $parametros = null,
        #[ArrayOf(PayloadLocationCompleta::class)]
        public readonly array $loc = [],
    ) {}
}
