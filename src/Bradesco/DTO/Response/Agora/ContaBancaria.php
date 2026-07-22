<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Conta bancaria cadastrada do cliente.
 *
 * Origem: components.schemas.BankAccountsDt.
 */
final class ContaBancaria implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo/nome do banco. */
        public readonly ?string $bank = null,
        /** Digito da agencia. */
        public readonly ?string $agencyDigit = null,
        /** Numero da agencia. */
        public readonly ?string $agency = null,
        /** Numero da conta. */
        public readonly ?string $account = null,
        /** Digito da conta. */
        public readonly ?string $digit = null,
        /** Tipo da conta. */
        public readonly ?string $typeAccount = null,
        /** Nome do co-titular. */
        public readonly ?string $nameCoHolder = null,
        /** CPF do co-titular. */
        public readonly ?int $cpfCoHolder = null,
        /** Indicador de conta principal. */
        public readonly ?string $mainAccount = null,
    ) {}
}
