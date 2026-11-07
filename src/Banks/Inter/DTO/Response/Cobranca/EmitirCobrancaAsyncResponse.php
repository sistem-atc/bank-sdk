<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `EmitirCobrancaAsyncResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `EmitirCobrancaAsyncResponse`).
 */
final class EmitirCobrancaAsyncResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código identificador único da cobranca */
        public readonly ?string $codigoSolicitacao = null,
    ) {}
}
