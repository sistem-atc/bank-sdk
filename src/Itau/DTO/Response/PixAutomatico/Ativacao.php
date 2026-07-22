<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `ativacao` da recorrência — dados da jornada de adesão pela qual o
 * pagador aprovou/aprovará a recorrência.
 */
final class Ativacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $tipoJornada = null,
        public readonly ?DadosJornada $dadosJornada = null,
    ) {}
}
