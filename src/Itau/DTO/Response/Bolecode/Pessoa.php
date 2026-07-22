<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Bolecode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pessoa` (nome + tipo de pessoa) do pagador / sacador avalista no
 * Bolecode Pix.
 */
final class Pessoa implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomePessoa = null,
        public readonly ?TipoPessoa $tipoPessoa = null,
    ) {}
}
