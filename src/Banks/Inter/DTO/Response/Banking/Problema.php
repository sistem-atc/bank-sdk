<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Problema` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Problema`).
 */
final class Problema implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** URI de referência que identifica o tipo de problema. De acordo com a RFC 7807. */
        public readonly ?string $type = null,
        /** Descrição resumida do problema. */
        public readonly ?string $title = null,
        /** Código HTTP do status retornado. */
        public readonly ?int $status = null,
        /** Descrição completa do problema. */
        public readonly ?string $detail = null,
        /** Data no formato timestamp do erro */
        public readonly ?string $timestamp = null,
    ) {}
}
