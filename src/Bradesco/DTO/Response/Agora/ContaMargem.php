<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Linhas da conta margem.
 *
 * Origem: components.schemas.MarginAccountResponse.
 */
final class ContaMargem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Primeira linha de margem. */
        public readonly ?float $firstLine = null,
        /** Segunda linha de margem. */
        public readonly ?float $secondLine = null,
    ) {}
}
