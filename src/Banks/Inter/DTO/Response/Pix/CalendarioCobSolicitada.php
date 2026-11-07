<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CalendarioCobSolicitada` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CalendarioCobSolicitada`).
 */
final class CalendarioCobSolicitada implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tempo de vida da cobrança, especificado em segundos a partir da data de criação (Calendario.criacao) */
        public readonly ?int $expiracao = null,
    ) {}
}
