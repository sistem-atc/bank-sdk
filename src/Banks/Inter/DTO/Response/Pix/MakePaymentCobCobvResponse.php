<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `MakePaymentCobCobvResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `MakePaymentCobCobvResponse`).
 */
final class MakePaymentCobCobvResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $endToEnd = null,
    ) {}
}
