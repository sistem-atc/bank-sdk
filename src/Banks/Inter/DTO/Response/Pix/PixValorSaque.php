<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PixValorSaque` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixValorSaque`).
 */
final class PixValorSaque implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Valor do Saque Pix. */
        public readonly ?string $valor = null,
        /** ##### Modalidade do Agente SIGLA Descrição AGTEC Agente Estabelecimento Comercial AGTOT Agente Outra Espécie de Pessoa Jurídica ou Correspondente no P… */
        public readonly ?string $modalidadeAgente = null,
        /** ISPB do Facilitador de Serviço de Saque */
        public readonly ?string $prestadorDoServicoDeSaque = null,
    ) {}
}
