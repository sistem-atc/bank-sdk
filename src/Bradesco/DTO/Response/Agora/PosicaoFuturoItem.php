<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em contratos futuros.
 *
 * Origem: components.schemas.ConsolidatedPositionFuturesApiData.
 */
final class PosicaoFuturoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo do contrato. */
        public readonly ?string $tickerCode = null,
        /** Data de vencimento. */
        public readonly ?string $maturityDate = null,
        /** Nome do ativo/emissor. */
        public readonly ?string $companyName = null,
        /** Nocional total (string no contrato). */
        public readonly ?string $totalNotional = null,
        /** Posicao atual. */
        public readonly ?int $actualPosition = null,
        /** Quantidade comprada. */
        public readonly ?int $buyQuantity = null,
        /** Quantidade no inicio do dia. */
        public readonly ?int $initialDayQuantity = null,
        /** Quantidade vendida. */
        public readonly ?int $sellQuantity = null,
        /** Valor de ajuste. */
        public readonly ?float $adjustValue = null,
        /** Preco atual. */
        public readonly ?float $currentPriceValue = null,
        /** Quantidade do dia. */
        public readonly ?int $qttyDay = null,
    ) {}
}
