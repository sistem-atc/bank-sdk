<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Violacao` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Violacao`).
 */
final class Violacao implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Descrição do erro */
        public readonly ?string $razao = null,
        /** Nome da propriedade */
        public readonly ?string $propriedade = null,
        /** Valor da propriedade */
        public readonly ?string $valor = null,
    ) {}
}
