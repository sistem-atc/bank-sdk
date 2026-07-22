<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Saldo global do cliente — engloba todos os saldos e limites.
 *
 * Origem: POST /managers-balance-check/v1/globalBalance/{cpfCnpj}/{accountCode}
 *      e  POST /managers-balance-check/v1/globalBalance/{cpfCnpj}/{accountCode}/{option}
 */
final class SaldoGlobalResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Resumo dos saldos projetados. */
        public readonly ?SaldoProjetadoResumo $projectedBalanceSummaryResponse = null,
        /** Saldo disponivel em conta corrente. */
        public readonly ?float $availableBalanceCC = null,
        /** Saldo disponivel atual. */
        public readonly ?float $availableBalanceActual = null,
        /** Saldo disponivel. */
        public readonly ?float $availableBalance = null,
        /** Saldo em opcoes. */
        public readonly ?float $optionBalance = null,
        /** Saldo em fundos (grafia do contrato: "foundsBalance"). */
        public readonly ?float $foundsBalance = null,
        /** Saldo em ouro. */
        public readonly ?float $goldBalance = null,
        /** Saldo em renda fixa. */
        public readonly ?float $fixedIncomeBalance = null,
        /** Saldo no Tesouro Direto. */
        public readonly ?float $treasuryDirectBalance = null,
        /** Saldo de resgates. */
        public readonly ?float $rescueBalance = null,
        /** Saldo em COE. */
        public readonly ?float $coeBalance = null,
        /** Saldo em renda variavel. */
        public readonly ?float $equitiesBalance = null,
        /** Saldo projetado. */
        public readonly ?float $projectedBalance = null,
        /** Limite da conta margem 1. */
        public readonly ?float $limitCM1 = null,
        /** Limite da conta margem 2. */
        public readonly ?float $limitCM2 = null,
        /** Valor reservado em IPO. */
        public readonly ?float $reserveIPO = null,
    ) {}
}
