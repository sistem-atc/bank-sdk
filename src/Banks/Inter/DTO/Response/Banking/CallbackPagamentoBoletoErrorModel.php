<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CallbackPagamentoBoletoErrorModel` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CallbackPagamentoBoletoErrorModel`).
 */
final class CallbackPagamentoBoletoErrorModel implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoErro = null,
        public readonly ?string $descricaoErro = null,
    ) {}
}
