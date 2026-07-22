<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em fundos de investimento.
 *
 * Origem: components.schemas.ConsolidatedPositionFundsApiData.
 */
final class PosicaoFundoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo da fonte. */
        public readonly ?int $sourceCode = null,
        /** Nome do fundo. */
        public readonly ?string $fund = null,
        /** Data de referencia da posicao. */
        public readonly ?string $referenceDate = null,
        /** Quantidade de cotas. */
        public readonly ?float $quotesQuantity = null,
        /** Posicao bruta. */
        public readonly ?float $grossPosition = null,
        /** Valor de IOF. */
        public readonly ?float $iofValue = null,
        /** Valor de IR. */
        public readonly ?float $irValue = null,
        /** Posicao liquida. */
        public readonly ?float $netPosition = null,
        /** Valor da cota. */
        public readonly ?float $quotesValue = null,
        /** Situacao do fundo. */
        public readonly ?string $status = null,
        /** Aberto para aplicacao. */
        public readonly ?bool $openForApplication = null,
        /** Aberto para resgate. */
        public readonly ?bool $openForRescue = null,
        /** Valor aplicado. */
        public readonly ?float $aplicatedValue = null,
        /** Rentabilidade (string no contrato). */
        public readonly ?string $rentability = null,
        /** Valor investido. */
        public readonly ?float $vlInvest = null,
        /** Valor atualizado. */
        public readonly ?float $vlUp = null,
        /** Valorizacao em valor. */
        public readonly ?float $vlApprec = null,
        /** Valorizacao percentual. */
        public readonly ?float $pcApprec = null,
        /** CNPJ do fundo. */
        public readonly ?string $cnpj = null,
    ) {}
}
