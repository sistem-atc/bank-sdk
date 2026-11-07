<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Devolucao` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Devolucao`).
 */
final class Devolucao implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id gerado pelo cliente para representar unicamente uma devolução. */
        public readonly ?string $id = null,
        /** ReturnIdentification que transita na PACS004. */
        public readonly ?string $rtrId = null,
        /** Valor a devolver. */
        public readonly ?string $valor = null,
        public readonly ?DevolucaoHorario $horario = null,
        /** Status da devolução. */
        public readonly ?string $status = null,
        /** # Status da Devolução Campo opcional que pode ser utilizado pelo PSP recebedor para detalhar os motivos de a devolução ter atingido o status em questã… */
        public readonly ?string $motivo = null,
    ) {}
}
