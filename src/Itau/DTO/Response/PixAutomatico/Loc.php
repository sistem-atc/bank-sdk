<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `loc` (location) do payload de recorrência. Retornado tanto embutido
 * na recorrência quanto pelos endpoints /locrec.
 */
final class Loc implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $location = null,
        public readonly ?string $criacao = null,
        public readonly ?string $idRec = null,
    ) {}
}
