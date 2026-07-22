<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `debitosListagem` de MgListaDebitosResponse.
 */
final class MgDebitoItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $descricaoTributo = null,  // ex.: "IPVA 2021-PARCELA 1"
        public readonly ?int $identificadorDebito = null,  // ex.: 2505210000004476050
        public readonly ?string $dataVencimento = null,  // ex.: "19/01/2021"
        public readonly ?float $valorTotal = null,  // ex.: 205.28
        public readonly ?float $valorMulta = null,  // ex.: 23.82
        public readonly ?float $valorDebito = null,  // ex.: 205.28
        public readonly ?float $valorBase = null,  // ex.: 119.1
        public readonly ?float $valorJuros = null,  // ex.: 62.36
    ) {}
}
