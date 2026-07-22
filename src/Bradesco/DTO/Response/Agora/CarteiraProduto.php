<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Produto (classe de ativo) no detalhamento da carteira.
 *
 * Origem: components.schemas.PortfolioListSummaryProductsApiData.
 */
final class CarteiraProduto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tipo do instrumento. */
        public readonly ?string $instrumentType = null,
        /** Descricao do produto. */
        public readonly ?string $description = null,
        /** Patrimonio liquido. */
        public readonly ?float $liquidPatrimony = null,
        /** Patrimonio bruto. */
        public readonly ?float $grossPatrimony = null,
        /** Total investido (custo). */
        public readonly ?float $purchaseTotal = null,
        /** Percentual do patrimonio. */
        public readonly ?float $percentagePatrimony = null,
        /** Valorizacao em valor. */
        public readonly ?float $valueAppreciation = null,
        /** Valorizacao percentual. */
        public readonly ?float $percentAppreciation = null,
    ) {}
}
