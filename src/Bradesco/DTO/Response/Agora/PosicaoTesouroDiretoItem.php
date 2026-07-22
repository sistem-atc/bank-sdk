<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada no Tesouro Direto.
 *
 * Origem: components.schemas.ConsolidatedPositionTreasuryDirectApiData.
 */
final class PosicaoTesouroDiretoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data de vencimento (AAAAMMDD numerico). */
        public readonly ?int $maturityDate = null,
        /** Indexador do titulo. */
        public readonly ?string $index = null,
        /** Nome do titulo. */
        public readonly ?string $bondName = null,
        /** Quantidade de titulos. */
        public readonly ?float $bondQuantity = null,
        /** Valor da posicao. */
        public readonly ?float $positionValue = null,
        /** Tipo do titulo. */
        public readonly ?string $bondType = null,
        /** Tipo de mercado. */
        public readonly ?string $marketType = null,
        /** Preco de compra. */
        public readonly ?float $purchasePrice = null,
        /** Data de vencimento (AAAAMMDD numerico, campo legado). */
        public readonly ?int $dtVencto = null,
        /** Valor aplicado liquido. */
        public readonly ?float $vlAplicLic = null,
        /** Valor bruto. */
        public readonly ?float $vlGross = null,
        /** Valor de origem. */
        public readonly ?float $vlOrig = null,
        /** Valorizacao em valor. */
        public readonly ?float $vlAppreciation = null,
        /** Valorizacao percentual. */
        public readonly ?float $percAppreciation = null,
        /** Preco de venda. */
        public readonly ?float $vlPriceSell = null,
        /** Quantidade em garantia. */
        public readonly ?float $guaranteeQuantity = null,
    ) {}
}
