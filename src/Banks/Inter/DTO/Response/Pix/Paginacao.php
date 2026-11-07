<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Paginacao` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Paginacao`).
 */
final class Paginacao implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Número da página recuperada. */
        public readonly ?int $paginaAtual = null,
        /** Quantidade de registros retornado na página. */
        public readonly ?int $itensPorPagina = null,
        /** Quantidade de páginas disponíveis para consulta. */
        public readonly ?int $quantidadeDePaginas = null,
        /** Quantidade total de itens disponíveis de acordo com os parâmetros informados. */
        public readonly ?int $quantidadeTotalDeItens = null,
    ) {}
}
