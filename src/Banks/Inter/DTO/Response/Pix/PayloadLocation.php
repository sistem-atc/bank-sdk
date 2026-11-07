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
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PayloadLocation`).
 */
final class PayloadLocation implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identificador da location a ser informada na criação da cobrança . */
        public readonly ?int $id = null,
        /** Localização do Payload a ser informada na criação da cobrança. */
        public readonly ?string $location = null,
        public readonly ?string $tipoCob = null,
        /** Data e hora em que a location foi criada. Respeita RFC 3339. */
        public readonly ?string $criacao = null,
    ) {}
}
