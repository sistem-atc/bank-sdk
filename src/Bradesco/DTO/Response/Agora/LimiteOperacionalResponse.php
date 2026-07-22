<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Limite operacional do cliente.
 *
 * O contrato devolve a mesma shape do saldo disponivel.
 *
 * Origem: POST /managers-balance-check/v1/operationallimit/{cpfCnpj}/{accountCode}
 */
final class LimiteOperacionalResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Limite operacional disponivel. */
        public readonly ?ConteudoSaldo $availableBalance = null,
    ) {}
}
