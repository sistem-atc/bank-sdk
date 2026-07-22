<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `pessoa` (nome + tipo_pessoa) das APIs de Boletos Cobrança.
 */
final class Pessoa implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nomePessoa = null,
        public readonly ?string $nomeFantasia = null,
        public readonly ?TipoPessoa $tipoPessoa = null,
    ) {}
}
