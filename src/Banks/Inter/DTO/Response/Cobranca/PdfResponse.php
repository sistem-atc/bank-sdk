<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PdfResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PdfResponse`).
 */
final class PdfResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Retorno do pdf base64 para String */
        public readonly ?string $pdf = null,
    ) {}
}
