<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta da consulta de saldo de contas PJ.
 *
 * Origem: GET /v1/fornecimento-saldos-contas/saldos
 *
 * O saldo não vem num campo único: vem como uma LISTA de produtos de saldo
 * (`lstLancamentosSaldos`) — "DISPONIVEL", "= TOTAL DE RECURSOS" etc. Use
 * `disponivel()` / `totalDeRecursos()` pra não depender da ordem da lista.
 */
final class SaldoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** Código do produto de saldo "DISPONIVEL". */
    public const PRODUTO_DISPONIVEL = 999;

    /** Código do produto de saldo "= TOTAL DE RECURSOS". */
    public const PRODUTO_TOTAL_RECURSOS = 995;

    public function __construct(
        /** Código de retorno tratado ('0' = sucesso). */
        public readonly ?string $codigoRetorno = null,
        /** Detalhe da mensagem de retorno do backend. */
        public readonly ?string $mensagem = null,
        /** Identificação do cliente (fixo "0"). Grafia do contrato. */
        public readonly ?string $identificaoCliente = null,
        /** Razão contábil da conta. */
        public readonly ?string $razaoConta = null,
        /** Número da conta bancária. */
        public readonly ?string $numeroConta = null,
        /** Dígito da conta bancária. */
        public readonly ?string $digitoConta = null,
        /** Nome do cliente/titular. */
        public readonly ?string $nomeCliente = null,
        /** Status da conta corrente: 0 OK, 1 bloqueada, 2 não habilitada, 4/5 judicial, 6 CCS. */
        public readonly ?string $statusContaCorrente = null,
        /** Status da conta poupança (mesma tabela da conta corrente). */
        public readonly ?string $statusContaPoupanca = null,
        /** Identificador da conta: conta corrente / conta poupança / conta INSS. */
        public readonly ?string $identificadorTipoConta = null,
        /** Status da cobertura automática: 0 não tem, 1 tem, 2 conta fácil automática. */
        public readonly ?string $statusCoberturaAutomatica = null,
        /** Número da conta-poupança fácil. */
        public readonly ?string $contaPoupancaFacil = null,
        /** Data da última atualização. */
        public readonly ?string $dataUltimaAtualizacao = null,
        /** Modalidade da conta: 1 poupança tradicional, 2 poupança fácil. */
        public readonly ?string $identificadorModalidadeConta = null,
        /** Data do próximo pagamento INSS (DDMMAAAA). */
        public readonly ?string $dataProximoPagamentoInss = null,
        /** Data de vencimento do cartão INSS (DDMMAAAA). */
        public readonly ?string $dataVencimentoCartaoInss = null,
        /** Status do cartão INSS: 0 OK, 1 vencido, 2 vencerá. */
        public readonly ?string $statusCartaoInss = null,
        /** Quantidade de linhas de saldo na lista (string no contrato). */
        public readonly ?string $quantidadeLancamentos = null,
        /** Composição do saldo, linha a linha. */
        #[ArrayOf(SaldoLancamento::class)]
        public readonly array $lstLancamentosSaldos = [],
    ) {}

    /** A consulta voltou com sucesso? (codigoRetorno '0') */
    public function sucesso(): bool
    {
        return $this->codigoRetorno === '0';
    }

    /** Linha de saldo por código de produto. */
    public function produto(int $codigoProduto): ?SaldoLancamento
    {
        foreach ($this->lstLancamentosSaldos as $linha) {
            if ($linha->codigoProduto === $codigoProduto) {
                return $linha;
            }
        }

        return null;
    }

    /** Saldo disponível (produto 999) em float, já com sinal. */
    public function disponivel(): ?float
    {
        return $this->produto(self::PRODUTO_DISPONIVEL)?->valor();
    }

    /** Total de recursos (produto 995) em float, já com sinal. */
    public function totalDeRecursos(): ?float
    {
        return $this->produto(self::PRODUTO_TOTAL_RECURSOS)?->valor();
    }
}
