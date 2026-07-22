<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta da consulta de extrato de contas PJ (ExtratoResponseDTO).
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos
 *
 * A resposta vem em TRÊS blocos independentes (cada um com o próprio cabeçalho
 * e código de retorno) — o banco preenche o que couber na janela consultada:
 *  - `extratoUltimosLancamentos`: saldo anterior + D-1 + dia corrente;
 *  - `extratoLancamentosFuturos`: agendados/previstos;
 *  - `extratoPorPeriodo`: histórico D-2 pra trás.
 *
 * PAGINAÇÃO: o contrato não expõe página/cursor. O recorte é a JANELA
 * (`dataInicio`/`dataFim`) — pra períodos longos, fatie a janela e concatene
 * `lancamentos()`. `quantidadeLancamentos` de cada bloco é o total daquele
 * bloco e serve de conferência.
 */
final class ExtratoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Bloco de saldo anterior + últimos lançamentos + lançamentos do dia. */
        #[ArrayOf(ExtratoUltimosLancamentos::class)]
        public readonly array $extratoUltimosLancamentos = [],
        /** Bloco de lançamentos futuros (agendados). */
        #[ArrayOf(ExtratoLancamentosFuturos::class)]
        public readonly array $extratoLancamentosFuturos = [],
        /** Bloco de extrato histórico por período (D-2 pra trás). */
        #[ArrayOf(ExtratoPorPeriodo::class)]
        public readonly array $extratoPorPeriodo = [],
    ) {}

    /**
     * Todos os lançamentos dos três blocos, achatados — a lista que a
     * conciliação bancária consome.
     *
     * @return array<int, Lancamento>
     */
    public function lancamentos(): array
    {
        $lancamentos = [];

        foreach ([$this->extratoUltimosLancamentos, $this->extratoLancamentosFuturos, $this->extratoPorPeriodo] as $blocos) {
            foreach ($blocos as $bloco) {
                $lancamentos = array_merge($lancamentos, $bloco->lancamentos());
            }
        }

        return $lancamentos;
    }

    /**
     * Só os lançamentos do histórico por período — o recorte fechado (D-2 pra
     * trás), sem os do dia nem os futuros.
     *
     * @return array<int, Lancamento>
     */
    public function lancamentosDoPeriodo(): array
    {
        $lancamentos = [];

        foreach ($this->extratoPorPeriodo as $bloco) {
            $lancamentos = array_merge($lancamentos, $bloco->lancamentos());
        }

        return $lancamentos;
    }

    /** Quantidade total de lançamentos declarada pelos blocos (conferência). */
    public function quantidadeDeclarada(): int
    {
        $total = 0;

        foreach ([$this->extratoUltimosLancamentos, $this->extratoLancamentosFuturos, $this->extratoPorPeriodo] as $blocos) {
            foreach ($blocos as $bloco) {
                $total += (int) ($bloco->quantidadeLancamentos ?? 0);
            }
        }

        return $total;
    }
}
