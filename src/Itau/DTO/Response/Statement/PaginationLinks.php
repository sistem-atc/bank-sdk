<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pagination.links` do Extrato Itaú — URLs relativas HATEOAS pra
 * navegar as páginas. `previous`/`next` vêm como string vazia quando não há
 * página anterior/seguinte.
 */
final class PaginationLinks implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $first = null,
        public readonly ?string $last = null,
        public readonly ?string $previous = null,
        public readonly ?string $next = null,
    ) {}
}
