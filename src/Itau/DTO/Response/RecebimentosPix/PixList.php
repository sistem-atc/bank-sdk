<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Listagem paginada de Pix recebidos — resposta de `GET /pix`.
 *
 * @property array<int, Pix> $pix
 */
final class PixList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Pix> $pix */
    public function __construct(
        public readonly ?Parametros $parametros = null,
        #[ArrayOf(Pix::class)]
        public readonly array $pix = [],
    ) {}
}
