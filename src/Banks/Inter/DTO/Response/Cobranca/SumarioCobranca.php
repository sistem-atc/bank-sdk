<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `SumarioCobranca` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `SumarioCobranca`).
 */
final class SumarioCobranca implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Possíveis status de uma cobranca. */
        public readonly ?string $situacao = null,
        /** Valor */
        public readonly ?float $valor = null,
        /** Número de cobranças com esta situação */
        public readonly ?int $quantidade = null,
    ) {}
}
