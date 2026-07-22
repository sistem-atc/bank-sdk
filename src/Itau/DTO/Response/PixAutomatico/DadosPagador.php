<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Dados do pagador da recorrência — resposta de
 * `GET /rec/{idRec}/dados-pagador`.
 */
final class DadosPagador implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idRec = null,
        public readonly ?string $status = null,
        public readonly ?Pagador $pagador = null,
    ) {}
}
