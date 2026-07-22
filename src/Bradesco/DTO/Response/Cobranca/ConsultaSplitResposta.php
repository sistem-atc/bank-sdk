<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Página da consulta de rateio de crédito (split payment) de um título.
 * Paginação por `indMaisPagina` = 'S' + reenvio de `restartSaida` em
 * `restartEntrada`.
 * Origem: POST /boleto/cobranca-consulta-split/v1/executar
 */
final class ConsultaSplitResposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código de status da operação. */
        public readonly ?int $status = null,
        /** Identificador único da transação. */
        public readonly ?string $transacao = null,
        /** Mensagem descritiva do resultado da operação. */
        public readonly ?string $mensagem = null,
        /** Causa detalhada do resultado da operação. */
        public readonly ?string $causa = null,
        /** Quantidade de itens na lista de rateio. */
        public readonly ?int $qlistaRteio = null,
        /** Indicador de existência de mais páginas. */
        public readonly ?string $indMaisPagina = null,
        /** Código de reinício para a próxima página de resultados. */
        public readonly ?string $restartSaida = null,
        /** Lista de informações de rateio. */
        #[ArrayOf(RateioCredito::class)] public readonly array $listaRteio = [],
    ) {}
}
