<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Consulta paginada de cobranças recorrentes — resposta de `GET /cobr`
 * (a lista vem na chave `cobsr`).
 */
final class CobrancaRecorrenteList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, CobrancaRecorrente> $cobsr */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(CobrancaRecorrente::class)]
        public readonly array $cobsr = [],
    ) {}
}
