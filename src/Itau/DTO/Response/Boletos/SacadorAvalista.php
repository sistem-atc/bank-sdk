<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `sacador_avalista` (opcional) das respostas de Boletos Cobrança.
 */
final class SacadorAvalista implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?Pessoa $pessoa = null,
        public readonly ?Endereco $endereco = null,
    ) {}
}
