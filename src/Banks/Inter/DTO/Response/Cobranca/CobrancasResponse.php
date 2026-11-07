<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CobrancasResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobrancasResponse`).
 *
 * @property array<int, CobrancaResponse> $cobrancas
 */
final class CobrancasResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Quantidade total de páginas disponíveis para consulta. */
        public readonly ?int $totalPaginas = null,
        /** Quantidade total de itens disponíveis de acordo com os parâmetros informados. */
        public readonly ?int $totalElementos = null,
        /** Quantidade de registros por página, configurado na requisição. */
        public readonly ?int $tamanhoPagina = null,
        /** Primeira página */
        public readonly ?bool $primeiraPagina = null,
        /** Última página */
        public readonly ?bool $ultimaPagina = null,
        /** Quantidade de registros retornado na página atual. */
        public readonly ?int $numeroDeElementos = null,
        #[ArrayOf(CobrancaResponse::class)]
        public readonly array $cobrancas = [],
    ) {}
}
