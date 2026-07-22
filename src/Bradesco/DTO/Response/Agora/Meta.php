<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Metadados de paginacao/consulta que acompanham as respostas de posicao.
 *
 * Origem: components.schemas.Meta (managers-position-mgmt).
 */
final class Meta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Numero total de registros no resultado. */
        public readonly ?int $totalRecords = null,
        /** Numero total de paginas. */
        public readonly ?int $totalPages = null,
        /** Data/hora da consulta (RFC-3339, UTC). */
        public readonly ?string $requestDateTime = null,
    ) {}
}
