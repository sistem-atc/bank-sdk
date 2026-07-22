<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `valor` do Pix Automático. Na recorrência vem `valorRec` (valor fixo)
 * OU `valorMinimoRecebedor` (valor variável); na cobrança recorrente vem
 * `original`. Valores monetários chegam como string decimal ("250.00").
 */
final class Valor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $valorRec = null,
        public readonly ?string $valorMinimoRecebedor = null,
        public readonly ?string $original = null,
    ) {}
}
