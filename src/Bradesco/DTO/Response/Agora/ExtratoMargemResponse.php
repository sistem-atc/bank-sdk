<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Extrato de uso da margem e do limite do cliente.
 *
 * Origem: POST /managers-statement/v1/marginlimit/{cpfCnpj}/{accountCode}/{startDate}/{endDate}
 */
final class ExtratoMargemResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Lancamentos de margem no periodo. @var array<int, ExtratoMargemItem> */
        #[ArrayOf(ExtratoMargemItem::class)]
        public readonly array $statement = [],
    ) {}
}
