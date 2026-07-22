<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Composicao de renda e patrimonio declarados pelo cliente.
 *
 * Origem: components.schemas.IncomeDt.
 */
final class DadosRenda implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Renda mensal. */
        public readonly ?float $monthlySalaryAmount = null,
        /** Valor em bens moveis. */
        public readonly ?float $movablesValue = null,
        /** Valor em imoveis. */
        public readonly ?float $valuePropertys = null,
        /** Valor em aplicacoes financeiras. */
        public readonly ?float $valueFinancialApplications = null,
        /** Outras rendas. */
        public readonly ?float $valueOtherIncome = null,
    ) {}
}
