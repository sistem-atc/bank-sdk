<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco de extrato histórico por período (D-2 pra trás) — é o bloco que
 * interessa pra conciliação bancária retroativa.
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos → extratoPorPeriodo[]
 */
final class ExtratoPorPeriodo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Código de retorno tratado ('0' = sucesso). */
        public readonly ?string $codigoRetorno = null,
        /** Detalhe da mensagem de retorno do backend. */
        public readonly ?string $mensagem = null,
        /** Identificação do cliente. Grafia do contrato. */
        public readonly ?string $identificaoCliente = null,
        /** Razão contábil da conta. */
        public readonly ?string $razaoConta = null,
        /** Número da conta bancária. */
        public readonly ?string $numeroConta = null,
        /** Dígito da conta bancária. */
        public readonly ?string $digitoConta = null,
        /** Nome do cliente/titular. */
        public readonly ?string $nomeCliente = null,
        /** Quantidade de registros retornados (string no contrato). */
        public readonly ?string $quantidadeLancamentos = null,
        /** Lançamentos do período consultado. */
        #[ArrayOf(Lancamento::class)]
        public readonly array $lstLancamentoMensal = [],
    ) {}

    /** @return array<int, Lancamento> */
    public function lancamentos(): array
    {
        return $this->lstLancamentoMensal;
    }
}
