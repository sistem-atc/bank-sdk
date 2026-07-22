<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Taxa cobrada sobre o uso da margem/limite.
 *
 * Origem: components.schemas.MarginLimitApiData.
 */
final class ExtratoMargemTaxaItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data de liquidacao. */
        public readonly ?string $settlementDate = null,
        /** Limite utilizado. */
        public readonly ?float $usedLimit = null,
        /** Valor de juros. */
        public readonly ?float $interestValue = null,
        /** Valor de IOF. */
        public readonly ?float $iof = null,
        /** Valor de IOF adicional. */
        public readonly ?float $additionalIOF = null,
    ) {}
}
