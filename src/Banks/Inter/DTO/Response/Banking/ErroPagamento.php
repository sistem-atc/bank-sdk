<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `ErroPagamento` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ErroPagamento`).
 */
final class ErroPagamento implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoErro = null,
        public readonly ?string $descricaoErro = null,
        public readonly ?string $codigoErroComplementar = null,
    ) {}
}
