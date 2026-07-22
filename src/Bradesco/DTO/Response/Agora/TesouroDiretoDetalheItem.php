<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao detalhada do Tesouro Direto.
 *
 * Origem: components.schemas.DetailedPositionTreasuryDirectApiData.
 */
final class TesouroDiretoDetalheItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Status da operacao. */
        public readonly ?string $operationStatus = null,
        /** Data de vencimento (AAAAMMDD numerico). */
        public readonly ?int $maturityDate = null,
        /** Data da aplicacao (AAAAMMDD numerico). */
        public readonly ?int $applicationDate = null,
        /** Dias corridos. */
        public readonly ?int $days = null,
        /** Nome do emissor. */
        public readonly ?string $issuerName = null,
        /** Nome do titulo. */
        public readonly ?string $bondName = null,
        /** Indexador. */
        public readonly ?string $index = null,
        /** Quantidade de titulos. */
        public readonly ?float $bondQuantity = null,
        /** Taxa contratada. */
        public readonly ?float $tax = null,
        /** Preco de compra. */
        public readonly ?float $purchasePrice = null,
        /** Preco de mercado. */
        public readonly ?float $marketPrice = null,
        /** Valor da posicao. */
        public readonly ?float $positionValue = null,
        /** Lucro apurado. */
        public readonly ?float $profitValue = null,
        /** Valor de IR. */
        public readonly ?float $irPrice = null,
        /** Valor liquido. */
        public readonly ?float $netValue = null,
        /** Valor de IOF. */
        public readonly ?float $iofPrice = null,
        /** Tipo de mercado. */
        public readonly ?string $marketType = null,
    ) {}
}
