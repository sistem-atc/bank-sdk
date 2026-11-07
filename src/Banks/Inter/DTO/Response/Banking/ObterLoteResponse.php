<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `ObterLoteResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ObterLoteResponse`).
 *
 * @property array<int, PagamentoLote> $pagamentos
 */
final class ObterLoteResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pagamento de um lote específico. */
        public readonly ?string $idLote = null,
        public readonly ?string $status = null,
        /** Identificador do lote */
        public readonly ?string $meuIdentificador = null,
        /** Qtde de pagamentos existentes no lote. */
        public readonly ?int $qtdePagamentos = null,
        /** Conta corrente responsável pelo pagamento do lote. */
        public readonly ?string $contaCorrente = null,
        /** Data em que o lote foi cadastrado. */
        public readonly ?string $dataCriacao = null,
        #[ArrayOf(PagamentoLote::class)]
        public readonly array $pagamentos = [],
    ) {}
}
