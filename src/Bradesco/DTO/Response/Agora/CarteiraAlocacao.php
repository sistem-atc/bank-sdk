<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Alocacao do patrimonio por classe de ativo.
 *
 * Origem: components.schemas.PortfolioSummaryAssetAllocationApi.
 */
final class CarteiraAlocacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Garantias. */
        public readonly ?CarteiraGrupoAtivo $collateral = null,
        /** Derivativos. */
        public readonly ?CarteiraGrupoAtivo $derivatives = null,
        /** Renda variavel. */
        public readonly ?CarteiraGrupoAtivo $equity = null,
        /** Renda fixa. */
        public readonly ?CarteiraGrupoAtivo $fixedIncome = null,
        /** Multimercado. */
        public readonly ?CarteiraGrupoAtivo $multimarket = null,
        /** Saldo projetado. */
        public readonly ?CarteiraGrupoAtivo $projectedBalance = null,
        /** Aluguel de acoes (BTC). */
        public readonly ?CarteiraGrupoAtivo $stockLending = null,
    ) {}
}
