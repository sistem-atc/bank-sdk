<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `RetryCallbackResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `RetryCallbackResponse`).
 */
final class RetryCallbackResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Lista dos códigos identificadores em que o reenvio foi solicitado com sucesso. */
        public readonly array $foundIds = [],
    ) {}
}
