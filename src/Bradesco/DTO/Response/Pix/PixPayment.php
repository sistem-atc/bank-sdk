<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resultado de um pagamento PIX Bradesco. PARCIAL POR DESIGN — campos a
 * confirmar com a spec real (end-to-end id, situação, etc.).
 */
final class PixPayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $identificador = null,
        public readonly ?string $endToEndId = null,
        public readonly ?string $situacao = null,
        public readonly ?float $valor = null,
        public readonly ?string $dataHora = null,
    ) {}
}
