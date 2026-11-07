<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Descontos aplicados à cobrança
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobVValorDesconto`).
 *
 * @property array<int, DescontoDataFixa> $descontoDataFixa
 */
final class CobVValorDesconto implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Abatimentos ou outras deduções aplicadas ao documento, em valor absoluto ou percentual do valor original do documento. */
        public readonly ?string $valorPerc = null,
        /** Descontos absolutos aplicados à cobrança. */
        #[ArrayOf(DescontoDataFixa::class)]
        public readonly array $descontoDataFixa = [],
        /** ##### Modalidade de desconto, conforme tabela de domínios. Descrição Domínio Valor Fixo até a[s] data[s] informada[s] 1 Percentual até a[s] data[s] in… */
        public readonly ?string $modalidade = null,
    ) {}
}
