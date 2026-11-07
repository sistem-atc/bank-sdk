<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `InformacoesPagamentoDarf` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `InformacoesPagamentoDarf`).
 */
final class InformacoesPagamentoDarf implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoSolicitacao = null,
        public readonly ?string $tipoDarf = null,
        public readonly ?float $valor = null,
        public readonly ?float $valorMulta = null,
        public readonly ?float $valorJuros = null,
        public readonly ?float $valorTotal = null,
        public readonly ?string $tipo = null,
        public readonly ?string $periodoApuracao = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $referencia = null,
        public readonly ?string $dataVencimento = null,
        public readonly ?string $codigoReceita = null,
        public readonly ?string $statusPagamento = null,
        public readonly ?string $dataInclusao = null,
        public readonly ?string $cnpjCpf = null,
        /** Aprovações necessárias para o pagamento ser aprovado. */
        public readonly ?int $aprovacoesNecessarias = null,
        /** Aprovações já realizadas. */
        public readonly ?int $aprovacoesRealizadas = null,
    ) {}
}
