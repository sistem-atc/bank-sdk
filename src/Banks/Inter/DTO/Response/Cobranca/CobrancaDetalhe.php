<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CobrancaDetalhe` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobrancaDetalhe`).
 *
 * @property array<int, DescontoResponse> $descontos
 */
final class CobrancaDetalhe implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identificador único da cobranca. */
        public readonly ?string $codigoSolicitacao = null,
        /** Seu Número. */
        public readonly ?string $seuNumero = null,
        /** Data de emissao da cobrança */
        public readonly ?string $dataEmissao = null,
        /** Data de vencimento da cobrança */
        public readonly ?string $dataVencimento = null,
        /** Valor Nominal da cobrança */
        public readonly ?float $valorNominal = null,
        /** Tipos de cobranças existentes. */
        public readonly ?string $tipoCobranca = null,
        /** Possíveis situações de uma cobranca. */
        public readonly ?string $situacao = null,
        /** Data que a situação da cobrança sofreu alteração. */
        public readonly ?string $dataSituacao = null,
        /** Valor total recebido, no caso da cobrança ter sido paga. */
        public readonly ?string $valorTotalRecebido = null,
        public readonly ?string $origemRecebimento = null,
        /** Motivo do Cancelamento. */
        public readonly ?string $motivoCancelamento = null,
        /** Cobrança está arquivada ou não */
        public readonly ?bool $arquivada = null,
        #[ArrayOf(DescontoResponse::class)]
        public readonly array $descontos = [],
        public readonly ?MultaMoraResponse $multa = null,
        public readonly ?MultaMoraResponse $mora = null,
        public readonly ?Pagador $pagador = null,
    ) {}
}
