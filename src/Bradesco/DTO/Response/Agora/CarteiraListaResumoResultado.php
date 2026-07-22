<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resultado do detalhamento da carteira por classe de ativo.
 *
 * Origem: components.schemas.PortfolioListSummaryResponseApiData.
 */
final class CarteiraListaResumoResultado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Patrimonio bruto total. */
        public readonly ?float $valuePatrimonyTotalGross = null,
        /** Produtos por classe de ativo. */
        public readonly ?CarteiraProdutos $products = null,
        /** Total investido (custo). */
        public readonly ?float $totalPurchaseTotal = null,
        /** Valorizacao percentual total. */
        public readonly ?float $percentAppreciationTotal = null,
        /** Valorizacao total em valor. */
        public readonly ?float $valueAppreciationTotal = null,
    ) {}
}
