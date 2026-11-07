<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * [DEPRECADO] Parâmetros utilizados para a realização de uma consulta de
 * cobranças.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ParametrosConsultaCob`).
 */
final class ParametrosConsultaCob implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data inicial utilizada na consulta. Exemplo: 2020-04-01T00:00:00Z */
        public readonly ?string $inicio = null,
        /** Data inicial utilizada na consulta. Exemplo: 2020-04-01T17:00:00Z */
        public readonly ?string $fim = null,
        /** Filtro pelo CPF do devedor. Não pode ser utilizado ao mesmo tempo que o CNPJ. */
        public readonly ?string $cpf = null,
        /** Filtro pelo CNPJ do devedor. Não pode ser utilizado ao mesmo tempo que o CPF. */
        public readonly ?string $cnpj = null,
        /** Filtro pela existência de location vinculada. */
        public readonly ?bool $locationPresente = null,
        /** Filtro pelo status das cobranças. */
        public readonly ?string $status = null,
        public readonly ?Paginacao $paginacao = null,
    ) {}
}
