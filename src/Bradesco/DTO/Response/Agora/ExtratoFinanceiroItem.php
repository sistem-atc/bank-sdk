<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lancamento do extrato financeiro.
 *
 * Origem: components.schemas.StatementApiResponseData.
 */
final class ExtratoFinanceiroItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data de referencia do lancamento. */
        public readonly ?string $referenceDate = null,
        /** Data de liquidacao. */
        public readonly ?string $settlementDate = null,
        /** Historico do lancamento. */
        public readonly ?string $description = null,
        /** Valor a debito. */
        public readonly ?float $debitValue = null,
        /** Valor a credito. */
        public readonly ?float $creditValue = null,
        /** Saldo apos o lancamento. */
        public readonly ?float $balanceValue = null,
    ) {}
}
