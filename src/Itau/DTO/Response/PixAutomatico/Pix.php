<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `pix` — o Pix efetivamente liquidado de uma cobrança recorrente
 * concluída (chega no callback do webhook de cobrança). Valor como string.
 */
final class Pix implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $endToEndId = null,
        public readonly ?string $txid = null,
        public readonly ?string $valor = null,
        public readonly ?string $horario = null,
        public readonly ?string $infoPagador = null,
    ) {}
}
