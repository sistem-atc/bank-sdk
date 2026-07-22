<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `calendario` de uma cobrança Pix (COB/COBV).
 *
 * Para COB imediato usa-se `expiracao` (segundos a partir da criação). Para COBV
 * usa-se `dataDeVencimento` (YYYY-MM-DD) + `validadeAposVencimento` (dias). Os
 * timestamps ficam como string (ISO) pra evitar parse de formatos irregulares.
 */
final class Calendario implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $criacao = null,
        public readonly ?int $expiracao = null,
        public readonly ?string $dataDeVencimento = null,
        public readonly ?int $validadeAposVencimento = null,
        public readonly ?string $apresentacao = null,
        public readonly ?string $liquidacao = null,
    ) {}
}
