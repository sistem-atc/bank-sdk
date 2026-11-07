<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `DescontoDataFixa` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `DescontoDataFixa`).
 */
final class DescontoDataFixa implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Descontos por pagamento antecipado, com data fixa. Matriz com até três elementos, sendo que cada elemento é composto por um par "data e valorPerc", pa… */
        public readonly ?string $data = null,
        /** Desconto em valor absoluto ou percentual por dia, útil ou corrido, conforme valor.desconto.modalidade */
        public readonly ?string $valorPerc = null,
    ) {}
}
