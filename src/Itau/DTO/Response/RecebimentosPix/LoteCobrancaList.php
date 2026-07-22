<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Listagem paginada de lotes de cobrança com vencimento — resposta de
 * `GET /lotecobv`.
 *
 * @property array<int, LoteCobranca> $lotes
 */
final class LoteCobrancaList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, LoteCobranca> $lotes */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(LoteCobranca::class)]
        public readonly array $lotes = [],
    ) {}
}
