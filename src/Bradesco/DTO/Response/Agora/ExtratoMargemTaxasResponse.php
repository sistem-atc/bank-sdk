<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Taxas de margem e limite do cliente na janela consultada.
 *
 * Origem: POST /managers-statement/v1/marginlimit-fees/{cpfCnpj}/{accountCode}/{startDate}/{endDate}
 */
final class ExtratoMargemTaxasResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Taxas do periodo. @var array<int, ExtratoMargemTaxaItem> */
        #[ArrayOf(ExtratoMargemTaxaItem::class)]
        public readonly array $fees = [],
    ) {}
}
