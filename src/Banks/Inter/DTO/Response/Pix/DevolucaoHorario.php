<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `DevolucaoHorario` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `DevolucaoHorario`).
 */
final class DevolucaoHorario implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Horário no qual a devolução foi solicitada no PSP. */
        public readonly ?string $solicitacao = null,
        /** Horário no qual a devolução foi liquidada no PSP. */
        public readonly ?string $liquidacao = null,
    ) {}
}
