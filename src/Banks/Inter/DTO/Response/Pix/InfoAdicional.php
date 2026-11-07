<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `InfoAdicional` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `InfoAdicional`).
 */
final class InfoAdicional implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Nome do campo. */
        public readonly ?string $nome = null,
        /** Dados do campo. */
        public readonly ?string $valor = null,
    ) {}
}
