<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Informações relacionadas ao saque
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Saque`).
 */
final class Saque implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Valor do saque efetuado */
        public readonly ?string $valor = null,
        /** Modalidade de alteração de valor do saque. Quando não preenchido o valor assumido é o 0 (zero). */
        public readonly ?int $modalidadeAlteracao = null,
        /** ##### Modalidade do Agente SIGLA Descrição AGTEC Agente Estabelecimento Comercial AGTOT Agente Outra Espécie de Pessoa Jurídica ou Correspondente no P… */
        public readonly ?string $modalidadeAgente = null,
        /** ISPB do Facilitador de Serviço de Saque */
        public readonly ?string $prestadorDoServicoDeSaque = null,
    ) {}
}
