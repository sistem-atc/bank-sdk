<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Parâmetros utilizados para a realização de uma consulta de locations.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ParametrosConsultaPayloadLocation`).
 */
final class ParametrosConsultaPayloadLocation implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data inicial utilizada na consulta. Respeita RFC 3339. */
        public readonly ?string $inicio = null,
        /** Data de fim utilizada na consulta. Respeita RFC 3339. */
        public readonly ?string $fim = null,
        /** Filtro pela existência de txid. */
        public readonly ?bool $txIdPresente = null,
        public readonly ?string $tipoCob = null,
        public readonly ?Paginacao $paginacao = null,
    ) {}
}
