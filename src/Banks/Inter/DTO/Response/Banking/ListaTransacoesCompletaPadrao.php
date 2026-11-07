<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `ListaTransacoesCompletaPadrao` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ListaTransacoesCompletaPadrao`).
 *
 * @property array<int, TransacaoCompleta> $transacoes
 */
final class ListaTransacoesCompletaPadrao implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Quantidade total de páginas disponíveis para consulta. */
        public readonly ?int $totalPaginas = null,
        /** Quantidade total de itens disponíveis de acordo com os parâmetros informados. */
        public readonly ?int $totalElementos = null,
        /** Última página */
        public readonly ?bool $ultimaPagina = null,
        /** Primeira página */
        public readonly ?bool $primeiraPagina = null,
        /** Quantidade de registros por página, configurado na requisição. */
        public readonly ?int $tamanhoPagina = null,
        /** Quantidade de registros retornado na página atual. */
        public readonly ?int $numeroDeElementos = null,
        #[ArrayOf(TransacaoCompleta::class)]
        public readonly array $transacoes = [],
    ) {}
}
