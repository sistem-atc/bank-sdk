<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Extrato financeiro do cliente na janela consultada.
 *
 * Origem: POST /managers-statement/v1/financial/{cpfCnpj}/{accountCode}/{startDate}/{endDate}
 */
final class ExtratoFinanceiroResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Lancamentos do periodo. @var array<int, ExtratoFinanceiroItem> */
        #[ArrayOf(ExtratoFinanceiroItem::class)]
        public readonly array $statement = [],
    ) {}
}
