<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Listagem paginada de cobranças — resposta de `GET /cob` e `GET /cobv`. As
 * cobranças vêm no array `cobs` (mesma shape pra imediata e com vencimento).
 *
 * @property array<int, Cobranca> $cobs
 */
final class CobrancaList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Cobranca> $cobs */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(Cobranca::class)]
        public readonly array $cobs = [],
    ) {}
}
