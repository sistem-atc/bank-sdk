<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PixCallbackPagador` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixCallbackPagador`).
 */
final class PixCallbackPagador implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nome = null,
        /** Caso o pagador seja uma pessoa física, o campo cpfCnpj virá mascarado. Ex: &#147;&#42;&#42;&#42;853226&#42;&#42;&#148; */
        public readonly ?string $cpfCnpj = null,
    ) {}
}
