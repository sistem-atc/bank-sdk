<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `vinculo` da recorrência: contrato, devedor e o objeto (descrição) do
 * débito recorrente.
 */
final class Vinculo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $contrato = null,
        public readonly ?Devedor $devedor = null,
        public readonly ?string $objeto = null,
    ) {}
}
