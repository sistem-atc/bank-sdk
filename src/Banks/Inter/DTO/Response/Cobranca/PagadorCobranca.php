<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PagadorCobranca` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PagadorCobranca`).
 */
final class PagadorCobranca implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nome = null,
        public readonly ?string $cpfCnpj = null,
    ) {}
}
