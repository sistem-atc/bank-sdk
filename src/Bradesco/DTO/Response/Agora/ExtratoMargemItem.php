<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lancamento do extrato de uso de margem/limite.
 *
 * Origem: components.schemas.MarginLimitApiTransactionData.
 */
final class ExtratoMargemItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Data de liquidacao. */
        public readonly ?string $settlementDate = null,
        /** Historico do lancamento. */
        public readonly ?string $description = null,
        /** Valor a debito. */
        public readonly ?float $debit = null,
        /** Valor a credito. */
        public readonly ?float $credit = null,
        /** Saldo apos o lancamento. */
        public readonly ?float $balance = null,
    ) {}
}
