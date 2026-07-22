<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `valor` de uma cobrança Pix (COB/COBV). `original` é sempre string
 * decimal ("110.00"). `modalidadeAlteracao` = 1 libera o pagador a alterar o
 * valor. Os encargos (multa/juros/abatimento/desconto) e a retirada só aparecem
 * em COBV / Saque-Troco.
 */
final class Valor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $original = null,
        public readonly ?int $modalidadeAlteracao = null,
        public readonly ?string $final = null,
        public readonly ?ValorEncargo $multa = null,
        public readonly ?ValorEncargo $juros = null,
        public readonly ?ValorEncargo $abatimento = null,
        public readonly ?ValorDesconto $desconto = null,
        public readonly ?Retirada $retirada = null,
    ) {}
}
