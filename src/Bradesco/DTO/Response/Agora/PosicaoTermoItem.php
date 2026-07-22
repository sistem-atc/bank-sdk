<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em operacoes a termo.
 *
 * Origem: components.schemas.ConsolidatedPositionTermsApiData.
 */
final class PosicaoTermoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo de negociacao do ativo. */
        public readonly ?string $symbol = null,
        /** Data de vencimento. */
        public readonly ?string $maturityDate = null,
        /** Data de vencimento (variante do contrato). */
        public readonly ?string $maturityDate3 = null,
        /** Preco do contrato. */
        public readonly ?float $price = null,
        /** Valor do contrato. */
        public readonly ?float $contractValue = null,
        /** Quantidade disponivel (string no contrato). */
        public readonly ?string $availableQuantity = null,
        /** Ultimo preco. */
        public readonly ?float $lastPrice = null,
        /** Valor atual. */
        public readonly ?float $currentValue = null,
        /** Resultado da operacao. */
        public readonly ?float $result = null,
        /** Percentual do resultado. */
        public readonly ?float $percentage = null,
        /** Data do pregao (AAAAMMDD numerico). */
        public readonly ?int $dataPreg = null,
        /** Data limite de rolagem (AAAAMMDD numerico). */
        public readonly ?int $dataLimRol = null,
        /** Preco atual do ativo-objeto. */
        public readonly ?float $currentAssetPrice = null,
    ) {}
}
