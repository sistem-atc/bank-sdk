<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `encerramento.cancelamento` da cobrança recorrente — quem solicitou o
 * cancelamento e o motivo (código + descrição).
 */
final class Cancelamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $solicitante = null,
        public readonly ?string $codigo = null,
        public readonly ?string $descricao = null,
    ) {}
}
