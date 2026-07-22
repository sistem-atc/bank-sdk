<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `valor.retirada` (Saque ou Troco Pix). `modalidadeAlteracao` = 1
 * permite o pagador alterar o valor; `modalidadeAgente` ∈ {AGTEC, AGTOT, AGPSS}.
 */
final class RetiradaItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $valor = null,
        public readonly ?int $modalidadeAlteracao = null,
        public readonly ?string $modalidadeAgente = null,
        public readonly ?string $prestadorDoServicoDeSaque = null,
    ) {}
}
