<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao consolidada em opcoes.
 *
 * Origem: components.schemas.ConsolidatedPositionOptionsApiData.
 */
final class PosicaoOpcaoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Fonte da posicao. */
        public readonly ?string $source = null,
        /** Tipo do papel. */
        public readonly ?string $securityType = null,
        /** Tipo da acao-objeto. */
        public readonly ?string $stockType = null,
        /** Codigo de negociacao da opcao. */
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
        /** Preco de exercicio (strike). */
        public readonly ?float $exercisePrice = null,
        /** Data de vencimento. */
        public readonly ?string $maturityDate = null,
        /** Ultimo preco. */
        public readonly ?float $lastPrice = null,
        /** Valor total. */
        public readonly ?float $totalPrice = null,
        /** Valor de origem (custo). */
        public readonly ?float $valueOrigin = null,
        /** Valorizacao em valor. */
        public readonly ?float $valueAppreciation = null,
        /** Valorizacao percentual. */
        public readonly ?float $percentAppreciation = null,
    ) {}
}
