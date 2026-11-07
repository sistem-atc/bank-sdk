<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `MultaMoraResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `MultaMoraResponse`).
 */
final class MultaMoraResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigo = null,
        public readonly ?float $taxa = null,
        public readonly ?float $valor = null,
    ) {}
}
