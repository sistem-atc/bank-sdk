<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `DescontoResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `DescontoResponse`).
 */
final class DescontoResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** * `VALORFIXODATAINFORMADA` - Valor fixo até a data informada. * `PERCENTUALDATAINFORMADA` - Percentual até a data informada. */
        public readonly ?string $codigo = null,
        /** Quantidade de dias antes do vencimento que será aplicado o desconto. */
        public readonly ?int $quantidadeDias = null,
        /** Taxa Percentual de Desconto do título. Máximo de duas casas decimais, valores excedentes são arredondados. Ex: 0.033 -> 0.03 | 0.049 -> 0.05 | 0.335 -… */
        public readonly ?float $taxa = null,
        /** Valor de Desconto, expresso na moeda do título. Máximo de duas casas decimais, valores excedentes são arredondados. Ex: 10.032 -> 10.03 | 0.249 -> 0.2… */
        public readonly ?float $valor = null,
    ) {}
}
