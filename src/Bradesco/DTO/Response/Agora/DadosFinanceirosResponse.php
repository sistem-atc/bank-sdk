<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Dados financeiros e bancarios cadastrais do cliente.
 *
 * Origem: POST /managers-cust-financial-info-update/v1/FinancialData/{cpfCnpj}
 */
final class DadosFinanceirosResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Profissao declarada. */
        public readonly ?string $profession = null,
        /** Indica se esta trabalhando. */
        public readonly ?bool $working = null,
        /** Empresa empregadora. */
        public readonly ?string $companyName = null,
        /** Composicao de renda e patrimonio. */
        public readonly ?DadosRenda $incomeData = null,
        /** Contas bancarias cadastradas. @var array<int, ContaBancaria> */
        #[ArrayOf(ContaBancaria::class)]
        public readonly array $bankAccountsData = [],
    ) {}
}
