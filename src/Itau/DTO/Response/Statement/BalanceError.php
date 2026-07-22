<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `error` de uma conta em `GET /balances` quando a resposta é HTTP 206
 * (saldo daquela conta indisponível). Ex.: {"error": "Serviço indisponível"}.
 */
final class BalanceError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $error = null,
    ) {}
}
