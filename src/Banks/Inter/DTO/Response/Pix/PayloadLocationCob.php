<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Identificador da localização do payload.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PayloadLocationCob`).
 */
final class PayloadLocationCob implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identificador da location a ser informada na criação da cobrança . */
        public readonly ?int $id = null,
        public readonly ?string $tipoCob = null,
    ) {}
}
