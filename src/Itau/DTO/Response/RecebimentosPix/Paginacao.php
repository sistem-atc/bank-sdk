<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `parametros.paginacao` das listagens (COB/COBV/PIX/LOC/LOTECOBV).
 */
final class Paginacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $paginaAtual = null,
        public readonly ?int $itensPorPagina = null,
        public readonly ?int $quantidadeDePaginas = null,
        public readonly ?int $quantidadeTotalDeItens = null,
    ) {}
}
