<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Rendimento diário de aplicação automática — item de `data[]` de
 * `GET /statements/{statementsId}/interest-bearing-accounts`. Valores vêm como
 * string decimal ("22700.62"), por isso são `?string` (mantém a precisão).
 */
final class InterestBearingIncome implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $date = null,
        public readonly ?string $grossAmountValue = null,
        public readonly ?string $netAmountValue = null,
        public readonly ?string $grossIncome = null,
        public readonly ?string $ir = null,
        public readonly ?string $iof = null,
    ) {}
}
