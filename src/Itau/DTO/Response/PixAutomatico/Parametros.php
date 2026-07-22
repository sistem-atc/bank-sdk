<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `parametros` ecoado nas consultas paginadas (janela + paginação +
 * filtros presentes na requisição).
 */
final class Parametros implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $inicio = null,
        public readonly ?string $fim = null,
        public readonly ?bool $idRecPresente = null,
        public readonly ?bool $locationPresente = null,
        public readonly ?Paginacao $paginacao = null,
    ) {}
}
