<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lançamento (`event`) do Extrato Itaú — `GET /statements/{statementsId}`.
 * É a unidade de conciliação bancária. `operation` ∈ {C (crédito), D (débito)}
 * e `reversal` indica estorno. `counterpart`/`origin` podem faltar conforme o
 * tipo de lançamento.
 */
final class StatementEvent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?string $operation = null,
        public readonly ?bool $reversal = null,
        public readonly ?EventDate $date = null,
        public readonly ?Literal $literal = null,
        public readonly ?Amount $amount = null,
        public readonly ?Counterpart $counterpart = null,
        public readonly ?Origin $origin = null,
    ) {}
}
