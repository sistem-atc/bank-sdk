<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Saldo do patrimonio (renda variavel) do cliente.
 *
 * Origem: POST /managers-balance-check/v1/equitiesBalance/{cpfCnpj}/{accountCode}
 */
final class SaldoPatrimonioResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Saldo do patrimonio em renda variavel. */
        public readonly ?ConteudoSaldo $equities = null,
    ) {}
}
