<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `UpdateCobrancaResponseBody` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `UpdateCobrancaResponseBody`).
 */
final class UpdateCobrancaResponseBody implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $mensagem = null,
        /** Identificador utilizado para consultar status da edição. */
        public readonly ?string $codigoEdicao = null,
    ) {}
}
