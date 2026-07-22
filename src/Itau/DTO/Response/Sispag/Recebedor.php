<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Sispag;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `recebedor` retornado na inclusão de Pix (SISPAG).
 */
final class Recebedor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $banco = null,
        public readonly ?string $agencia = null,
        public readonly ?string $conta = null,
        public readonly ?string $documento = null,
        public readonly ?string $nome = null,
        public readonly ?string $identificacaoChave = null,
    ) {}
}
