<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixTransferencias;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco de paginação das consultas de transferência.
 *
 * Schema `Paginacao` da spec "Pix Transferência - Consultar".
 */
final class Paginacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Número da página recuperada. */
        public readonly ?int $paginaAtual = null,
        /** Quantidade de registros retornados na página. */
        public readonly ?int $itensPorPagina = null,
        /** Quantidade de páginas disponíveis para consulta. */
        public readonly ?int $quantidadeDePaginas = null,
        /** Quantidade total de itens disponíveis para os parâmetros informados. */
        public readonly ?int $quantidadeTotalDeItens = null,
    ) {}
}
