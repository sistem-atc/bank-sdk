<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em renda variavel (acoes).
 *
 * Origem: components.schemas.ConsolidatedPositionEquitiesApiData.
 */
final class PosicaoAcaoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Fonte da posicao. */
        public readonly ?string $source = null,
        /** Tipo do papel (grafia do contrato: "secutiryType"). */
        public readonly ?string $secutiryType = null,
        /** Codigo de negociacao do ativo. */
        public readonly ?string $symbol = null,
        /** Nome do instrumento. */
        public readonly ?string $instrumentName = null,
        /** Nome da empresa emissora. */
        public readonly ?string $companyName = null,
        /** Quantidade disponivel. */
        public readonly ?int $availableQuantity = null,
        /** Quantidade bloqueada. */
        public readonly ?int $blockedQuantity = null,
        /** Quantidade em garantia. */
        public readonly ?int $collateral = null,
        /** Quantidade total. */
        public readonly ?int $quantity = null,
        /** Preco medio. */
        public readonly ?float $averagePrice = null,
        /** Ultimo preco. */
        public readonly ?float $lastPrice = null,
        /** Valor atual da posicao. */
        public readonly ?float $currentValue = null,
        /** Valor de origem (custo). */
        public readonly ?float $valueOrigin = null,
        /** Valorizacao em valor. */
        public readonly ?float $valueAppreciation = null,
        /** Valorizacao percentual. */
        public readonly ?float $percentAppreciation = null,
        /** Quantidade de titulos. */
        public readonly ?float $quantityTitles = null,
        /** Quantidade doada em BTC (aluguel). */
        public readonly ?int $quantityBtc = null,
    ) {}
}
