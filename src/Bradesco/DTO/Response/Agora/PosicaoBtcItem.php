<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em BTC (aluguel de ativos).
 *
 * Origem: components.schemas.ConsolidatedPositionBtcApiData.
 */
final class PosicaoBtcItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo de negociacao do ativo. */
        public readonly ?string $symbol = null,
        /** Ponta da operacao (doador/tomador). */
        public readonly ?string $side = null,
        /** Quantidade. */
        public readonly ?int $quantity = null,
        /** Taxa do contrato. */
        public readonly ?float $tax = null,
        /** Data de abertura. */
        public readonly ?string $openDate = null,
        /** Data de vencimento. */
        public readonly ?string $maturityDate = null,
        /** Preco do contrato. */
        public readonly ?float $contractPrice = null,
        /** Valor do contrato. */
        public readonly ?float $contractValue = null,
        /** Ultimo preco. */
        public readonly ?float $lastPrice = null,
        /** Valor atual. */
        public readonly ?float $currentValue = null,
        /** Nome do instrumento. */
        public readonly ?string $instrumentName = null,
        /** Valor liquido. */
        public readonly ?float $vlLiq = null,
    ) {}
}
