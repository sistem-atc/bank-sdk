<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `valor.retirada` de uma COB — Saque e Troco Pix (contratação à parte).
 * Pode conter `saque` OU `troco` exclusivamente.
 */
final class Retirada implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?RetiradaItem $saque = null,
        public readonly ?RetiradaItem $troco = null,
    ) {}
}
