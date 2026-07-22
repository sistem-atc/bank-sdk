<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de posicao detalhada de renda fixa.
 *
 * Origem: components.schemas.DetailedFixedIncomeApiData.
 */
final class RendaFixaItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Nome do titulo. */
        public readonly ?string $bondName = null,
        /** Tipo do titulo. */
        public readonly ?string $bondType = null,
        /** Nome do emissor. */
        public readonly ?string $issuerName = null,
        /** Data de vencimento. */
        public readonly ?string $maturityDate = null,
        /** Data da aplicacao. */
        public readonly ?string $applicationDate = null,
        /** Data de referencia. */
        public readonly ?string $referenceDate = null,
        /** Quantidade de titulos. */
        public readonly ?int $bondQuantity = null,
        /** Valor bruto. */
        public readonly ?float $grossValue = null,
        /** Taxa do titulo (string no contrato). */
        public readonly ?string $bondRate = null,
        /** Tipo de resgate. */
        public readonly ?string $redeemType = null,
        /** Valor unitario atual do titulo. */
        public readonly ?float $bondUnitValue = null,
        /** Valor unitario na compra. */
        public readonly ?float $purchaseBondUnitValue = null,
        /** Valor de IR do titulo. */
        public readonly ?float $bondTaxValue = null,
        /** Valor de IOF. */
        public readonly ?float $iofTaxValue = null,
        /** Percentual de IR (string no contrato). */
        public readonly ?string $bondTaxPercentage = null,
        /** Valor liquido. */
        public readonly ?float $netValue = null,
        /** Caminho da nota de corretagem. */
        public readonly ?string $brokerageNotePath = null,
        /** Possui liquidez diaria. */
        public readonly ?bool $dailyLiquidity = null,
        /** Identificador do titulo. */
        public readonly ?int $bondId = null,
        /** Identificador do tipo de taxa. */
        public readonly ?int $taxTypeId = null,
        /** Codigo da fonte. */
        public readonly ?int $sourceCode = null,
        /** Regra operacional de precificacao. */
        public readonly ?string $precificationOperationalRule = null,
        /** Percentual pre-fixado. */
        public readonly ?float $preTaxPercentage = null,
        /** Percentual do indexador. */
        public readonly ?float $indexerPercentage = null,
        /** Valor aplicado. */
        public readonly ?float $appliedValue = null,
        /** Descricao da taxa do titulo. */
        public readonly ?string $bondTaxDescription = null,
        /** Valor de origem. */
        public readonly ?float $valueOrigin = null,
        /** Valorizacao em valor. */
        public readonly ?float $valueAppreciation = null,
        /** Valorizacao percentual. */
        public readonly ?float $percentAppreciation = null,
        /** Valor de compra (string no contrato). */
        public readonly ?string $valuePurchaseBuy = null,
        /** Codigo CETIP/SELIC. */
        public readonly ?string $cetipSelicCode = null,
        /** Quantidade em garantia. */
        public readonly ?float $guaranteeQuantity = null,
    ) {}
}
