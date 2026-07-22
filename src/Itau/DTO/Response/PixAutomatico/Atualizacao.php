<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item do histórico `atualizacao` (mudanças de status ao longo do tempo).
 * Dependendo da entidade, o status vem em `status` ou em `nome`.
 */
final class Atualizacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $data = null,
        public readonly ?string $status = null,
        public readonly ?string $nome = null,
    ) {}
}
