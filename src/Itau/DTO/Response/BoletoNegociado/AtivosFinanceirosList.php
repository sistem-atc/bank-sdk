<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta de `GET /boleto/v1/ativos-financeiros` — lista embrulhada em `data`.
 *
 * @property list<AtivoFinanceiro> $itens
 */
final class AtivosFinanceirosList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<AtivoFinanceiro> $itens */
    public function __construct(
        #[ArrayOf(AtivoFinanceiro::class)]
        public readonly array $itens = [],
    ) {}
}
