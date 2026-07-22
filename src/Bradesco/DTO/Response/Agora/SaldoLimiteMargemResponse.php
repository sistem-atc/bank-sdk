<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Saldo do limite de margem do cliente.
 *
 * Origem: POST /managers-balance-check/v1/marginLimitBalance/{cpfCnpj}/{accountCode}
 */
final class SaldoLimiteMargemResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Valor e limite de margem. */
        public readonly ?SaldoValorLimite $balance = null,
        /** Linhas da conta margem. */
        public readonly ?ContaMargem $marginAccount = null,
    ) {}
}
