<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resumo dos saldos projetados D0..D3.
 *
 * Origem: components.schemas.ProjectedBalanceSummaryResponse.
 */
final class SaldoProjetadoResumo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Saldo disponivel projetado. */
        public readonly ?float $projectAvailableBalance = null,
        /** Saldo bloqueado. */
        public readonly ?float $blockedBalance = null,
        /** Saldo projetado calculado. */
        public readonly ?float $calculatedProjectedBalance = null,
        /** Saldo projetado em D+1. */
        public readonly ?float $projectedBalanceD1 = null,
        /** Saldo projetado em D+2. */
        public readonly ?float $projectedBalanceD2 = null,
        /** Saldo projetado em D+3. */
        public readonly ?float $projectedBalanceD3 = null,
        /** Saldo resgatado. */
        public readonly ?float $redeemedBalance = null,
        /** Data do saldo D0. */
        public readonly ?string $balanceDateD0 = null,
        /** Data do saldo D+1. */
        public readonly ?string $balanceDateD1 = null,
        /** Data do saldo D+2. */
        public readonly ?string $balanceDateD2 = null,
        /** Data do saldo D+3. */
        public readonly ?string $balanceDateD3 = null,
        /** Data do pregao de referencia. */
        public readonly ?string $tradingSessionDate = null,
    ) {}
}
