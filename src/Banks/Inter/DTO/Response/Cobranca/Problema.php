<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Problema` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Problema`).
 *
 * @property array<int, Violacao> $violacoes
 */
final class Problema implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código HTTP do status retornado. */
        public readonly ?int $status = null,
        /** Descrição resumida do problema. */
        public readonly ?string $title = null,
        /** Descrição completa do problema. */
        public readonly ?string $detail = null,
        #[ArrayOf(Violacao::class)]
        public readonly array $violacoes = [],
    ) {}
}
