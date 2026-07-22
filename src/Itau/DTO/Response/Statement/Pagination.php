<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pagination` do Extrato Itaú — presente em `GET /statements/{id}` e em
 * `GET /statements/{id}/judicial-orders`. Traz os links HATEOAS e os contadores
 * da paginação.
 */
final class Pagination implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?PaginationLinks $links = null,
        public readonly ?int $page = null,
        public readonly ?int $totalPages = null,
        public readonly ?int $totalElements = null,
        public readonly ?int $pageSize = null,
    ) {}
}
