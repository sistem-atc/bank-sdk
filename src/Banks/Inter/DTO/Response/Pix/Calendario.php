<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Os campos aninhados sob o identificador calendário organizam informações a
 * respeito de controle de tempo da cobrança.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Calendario`).
 */
final class Calendario implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tempo de vida da cobrança, especificado em segundos a partir da data de criação (Calendario.criacao) */
        public readonly ?int $expiracao = null,
        public readonly ?string $criacao = null,
    ) {}
}
