<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `InformacoesPagamento` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `InformacoesPagamento`).
 */
final class InformacoesPagamento implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoTransacao = null,
        public readonly ?string $codigoBarra = null,
        public readonly ?string $tipo = null,
        public readonly ?string $dataVencimentoDigitada = null,
        public readonly ?string $dataVencimentoTitulo = null,
        public readonly ?string $dataInclusao = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?float $valorPago = null,
        public readonly ?float $valorNominal = null,
        public readonly ?string $statusPagamento = null,
        /** Aprovações necessárias para o pagamento ser aprovado. */
        public readonly ?float $aprovacoesNecessarias = null,
        /** Aprovações já realizadas. */
        public readonly ?float $aprovacoesRealizadas = null,
        public readonly ?string $cpfCnpjBeneficiario = null,
        public readonly ?string $nomeBeneficiario = null,
        public readonly ?string $autenticacao = null,
        /** Número sequencial único, exclusivo para uso no Inter. */
        public readonly ?string $nsu = null,
    ) {}
}
