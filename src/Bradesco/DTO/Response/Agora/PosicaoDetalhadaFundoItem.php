<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao detalhada de fundo (por fonte).
 *
 * Origem: components.schemas.DetailedPositionFundsApiData.
 */
final class PosicaoDetalhadaFundoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo da fonte. */
        public readonly ?int $sourceCode = null,
        /** Nome do fundo. */
        public readonly ?string $fund = null,
        /** Data de referencia. */
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
        /** Data do certificado. */
        public readonly ?string $certificateDate = null,
    ) {}
}
