<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\SaldoExtrato;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco "últimos lançamentos" do extrato (D-1 e dia corrente), com o cabeçalho
 * de identificação da conta.
 *
 * Origem: GET /v1/fornecimento-extratos-contas/extratos → extratoUltimosLancamentos[]
 */
final class ExtratoUltimosLancamentos implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

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
        /** Quantidade de lançamentos na lista (o banco devolve como string). */
        public readonly ?string $quantidadeLancamentos = null,
        /** Grupos de lançamentos (saldo anterior / últimos / do dia). */
        #[ArrayOf(GrupoLancamentos::class)]
        public readonly array $listaLancamentos = [],
    ) {}

    /**
     * Achata os grupos num único fluxo de lançamentos.
     *
     * @return array<int, Lancamento>
     */
    public function lancamentos(): array
    {
        $lancamentos = [];

        foreach ($this->listaLancamentos as $grupo) {
            $lancamentos = array_merge($lancamentos, $grupo->todos());
        }

        return $lancamentos;
    }
}
