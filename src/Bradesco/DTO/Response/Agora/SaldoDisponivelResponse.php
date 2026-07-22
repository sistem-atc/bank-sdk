<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Saldo disponivel do cliente.
 *
 * Origem: POST /managers-balance-check/v1/availableBalance/{cpfCnpj}/{accountCode}
 */
final class SaldoDisponivelResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Saldo disponivel. */
        public readonly ?ConteudoSaldo $availableBalance = null,
    ) {}
}
