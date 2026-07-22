<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `dadosQR` da recorrência — jornada de adesão e o Pix Copia e Cola
 * (payload EMV do QR Code composto).
 */
final class DadosQR implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $jornada = null,
        public readonly ?string $pixCopiaECola = null,
    ) {}
}
