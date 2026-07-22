<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `dadosJornada` da ativação — carrega o `txid` da cobrança imediata
 * vinculada na jornada de adesão (jornada 3).
 */
final class DadosJornada implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $tipoJornada = null,
        public readonly ?string $txid = null,
    ) {}
}
