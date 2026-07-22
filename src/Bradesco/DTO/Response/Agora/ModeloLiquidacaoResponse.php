<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Modelo de liquidacao do cliente (0 = Agora, 1 = Bradesco).
 *
 * Origem: POST /managers-settlement/v1/ModelSettlement/{cblc}/{cpf}
 */
final class ModeloLiquidacaoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Modelo de liquidacao apurado. */
        public readonly ?RetornoStatus $regress = null,
    ) {}
}
