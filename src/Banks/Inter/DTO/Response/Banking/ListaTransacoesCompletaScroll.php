<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Resposta para consultas usando modo scroll. Retornado quando headers
 * `x-inter-scroll-enabled` ou `x-inter-scroll-id` são utilizados.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ListaTransacoesCompletaScroll`).
 *
 * @property array<int, TransacaoCompleta> $transacoes
 */
final class ListaTransacoesCompletaScroll implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Quantidade total de itens disponíveis de acordo com os parâmetros informados. */
        public readonly ?int $totalElementos = null,
        /** ID único do scroll para continuar a navegação. Use este valor no header `x-inter-scroll-id` das próximas requisições. Quando `hasMore: false`, este ca… */
        public readonly ?string $scrollId = null,
        /** Indica se existem mais transações para serem recuperadas. Quando `false`, o scroll é automaticamente removido. */
        public readonly ?bool $hasMore = null,
        #[ArrayOf(TransacaoCompleta::class)]
        public readonly array $transacoes = [],
    ) {}
}
