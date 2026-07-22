<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Proposta de previdencia implantada do cliente.
 *
 * Origem: components.schemas.ImplantedProposal.
 */
final class PrevidenciaProposta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Matricula. */
        public readonly ?int $registration = null,
        /** Serie da proposta. */
        public readonly ?string $proposedSeries = null,
        /** Numero da proposta. */
        public readonly ?int $proposedNumber = null,
        /** Nome do participante. */
        public readonly ?string $nameParticipant = null,
        /** Centro de custo da proposta. */
        public readonly ?string $centerProposedCost = null,
        /** Codigo do plano. */
        public readonly ?string $planCod = null,
        /** Nome do plano. */
        public readonly ?string $planName = null,
        /** Data da venda (AAAAMMDD numerico). */
        public readonly ?int $saleDate = null,
        /** Categoria internet. */
        public readonly ?string $internetCategory = null,
        /** Codigo do formulario. */
        public readonly ?int $formCode = null,
        /** Saldo atual. */
        public readonly ?float $valueCurrentBalance = null,
        /** Sinal do saldo atual. */
        public readonly ?string $valueCurrentBalanceSign = null,
        /** Data do saldo atual (AAAAMMDD numerico). */
        public readonly ?int $currentBalanceDate = null,
        /** Codigo da empresa. */
        public readonly ?string $companyCode = null,
        /** Descricao do fundo. */
        public readonly ?string $descriptionFund = null,
        /** Saldo da empresa (parcela 1). */
        public readonly ?float $valueBalanceCompany1 = null,
        /** Sinal do saldo da empresa (parcela 1). */
        public readonly ?string $valueBalanceCompany1Sign = null,
        /** Saldo do participante (parcela 1). */
        public readonly ?float $valueParticipantBalance1 = null,
        /** Sinal do saldo do participante (parcela 1). */
        public readonly ?string $valueParticipantBalance1Signal = null,
        /** Ano do saldo bloqueado 1. */
        public readonly ?int $yearBalanceBlocked1 = null,
        /** Valor do saldo bloqueado 1. */
        public readonly ?float $valueBalanceBlocked1 = null,
        /** Ano do saldo bloqueado 2. */
        public readonly ?int $yearBalanceBlocked2 = null,
        /** Valor do saldo bloqueado 2. */
        public readonly ?float $valueBalanceBlocked2 = null,
        /** Valor total disponivel. */
        public readonly ?float $totalValueAvailable = null,
        /** Sinal do valor total disponivel. */
        public readonly ?string $valueTotalAvailableSignal = null,
        /** Identificador de origem Kirton. */
        public readonly ?string $identifiesKirtonOrigin = null,
        /** Indicador de exibicao do registro. */
        public readonly ?string $demonstrateRecord = null,
        /** Tipo do plano. */
        public readonly ?int $typePlan = null,
        /** Regime de tributacao. */
        public readonly ?string $regime = null,
        /** Codigo de transferencia do extrato. */
        public readonly ?string $transferCodeExtract = null,
        /** Quantidade de beneficios. */
        public readonly ?int $quantityBenefits = null,
        /** Beneficios implantados. @var array<int, PrevidenciaBeneficio> */
        #[ArrayOf(PrevidenciaBeneficio::class)]
        public readonly array $benefits = [],
        /** Disclaimer de contribuicao (CVM 179). */
        public readonly ?string $disclaimerContributionCVM179 = null,
    ) {}
}
