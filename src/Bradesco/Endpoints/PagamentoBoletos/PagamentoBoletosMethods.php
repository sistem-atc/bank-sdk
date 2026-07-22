<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PagamentoBoletos;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\AlteracaoAgendamento;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\EfetivacaoPagamento;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\ExclusaoAgendamento;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\ListaAgendamentos;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\ListaPagamentosDevolvidos;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\PagamentoEspecifico;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\ParametrosPagamento;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\PreEfetivacaoPagamento;
use SistemAtc\Banks\Bradesco\DTO\Response\PagamentoBoletos\ValidacaoTitulo;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;

/**
 * Pagamento de boletos de cobrança — Bradesco (família open_api,
 * base `/boleto/pagamento-cobranca/v1`).
 *
 * ⚠️ ESTE PRODUTO MOVIMENTA DINHEIRO. A `efetivar()` DEBITA a conta (ou agenda
 * o débito). Respeite o fluxo real da API, nesta ordem:
 *
 *   0. `consultarParametros()`  — (opcional, recomendado) limites e horários
 *      da conta pagadora. Serve pra abortar antes de tentar pagar fora da
 *      grade ou acima do limite disponível.
 *   1. `validarTitulo()`        — abre o título pelo código de barras ou linha
 *      digitável: beneficiário, vencimento calculado, valor cobrado, faixa de
 *      valor aceita e `numeroCtrlCip` (quando `consultaCip = S`).
 *   2. `preEfetivar()`          — simulação/reserva: devolve o valor que SERÁ
 *      debitado e o `nroProtocolo`. Ainda NÃO debita.
 *   3. `efetivar()`             — executa/agenda o pagamento de fato. É aqui
 *      que o dinheiro sai. Guarde o `nroProtocolo` retornado: é a chave para
 *      consultar, alterar e excluir depois.
 *
 * Pós-pagamento: `listarAgendamentos()`, `consultarPagamento()`,
 * `alterarAgendamento()`, `excluirAgendamento()` e `listarDevolvidos()`.
 *
 * NOTA: o Bradesco usa POST em TODAS as operações, inclusive nas consultas —
 * não "corrija" para GET. Cada operação tem seu próprio microserviço sob a
 * base do produto.
 */
final class PagamentoBoletosMethods extends BaseMethods implements PaymentsEndpoint
{
    /** Base do produto (server da spec menos o host). */
    private const BASE = '/boleto/pagamento-cobranca/v1';

    private const PATH_PARAMETROS = self::BASE.'/cobranca-parametros-pgto/executar';

    private const PATH_VALIDA_TITULO = self::BASE.'/cobranca-valida-titulo-pagamento/validaTituloPagamento';

    private const PATH_PRE_EFETIVACAO = self::BASE.'/cobranca-pre-efetivacao/pre-efetivacao-pagamento';

    private const PATH_EFETIVACAO = self::BASE.'/cobranca-efetivacao/solicitacao/executar';

    private const PATH_LISTA_AGENDAMENTOS = self::BASE.'/cobranca-agendamentos-pgto/listar';

    private const PATH_ALTERA_AGENDAMENTO = self::BASE.'/cobranca-alterar-agendamento/alteracao/executar';

    private const PATH_EXCLUI_AGENDAMENTO = self::BASE.'/cobranca-excluir-agendamento/exclusao/executar';

    private const PATH_CONSULTA_PAGAMENTO = self::BASE.'/cobranca-pagamento-consulta/consulta-pagamento-especifico';

    private const PATH_LISTA_DEVOLVIDOS = self::BASE.'/cobranca-lista-pagamento-devolvido/listar';

    /**
     * PASSO 0 — Consulta de parâmetros e limites de pagamento da conta.
     *
     * @param  array{bancoCliente?: string, agenciaCliente: string, digitoAgencia?: string, contaCliente: string, digitoConta?: string}  $dados
     */
    public function consultarParametros(array $dados): ParametrosPagamento
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_PARAMETROS, body: $dados);

        return ParametrosPagamento::fromArray($data);
    }

    /**
     * PASSO 1 — Valida o título de pagamento (abre o boleto).
     *
     * `dadosEntrada` exige DEZ ZEROS antes do código de barras / linha
     * digitável; `tipoEntrada` = 1 (código de barras) ou 2 (linha digitável).
     *
     * @param  array{tipoEntrada: int, dadosEntrada: string, bancoTitulo?: int, agenciaDeb?: int, contaDeb?: int}  $dados
     */
    public function validarTitulo(array $dados): ValidacaoTitulo
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_VALIDA_TITULO, body: $dados);

        return ValidacaoTitulo::fromArray($data);
    }

    /**
     * PASSO 2 — Pré-efetivação: simula o pagamento e devolve o valor final e o
     * protocolo. NÃO debita a conta.
     *
     * @param  array<string, mixed>  $dados  Obrigatórios: codigoAgencia, formaCaptura,
     *                                       codigoBarras, dataVencimento, valorTitulo, dataMovimento, dataPagamento,
     *                                       horaTransacao, formaPagamento, bancoDebito, agenciaDebito, contaDebito,
     *                                       cnpjCpfPtdor, filialCnpjPtdor, ctrlCnpjPtdor.
     */
    public function preEfetivar(array $dados): PreEfetivacaoPagamento
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_PRE_EFETIVACAO, body: $dados);

        return PreEfetivacaoPagamento::fromArray($data);
    }

    /**
     * PASSO 3 — Efetivação do pagamento. ⚠️ DEBITA A CONTA (ou agenda o débito
     * quando `dataPagamento` for futura). Só chame depois de `validarTitulo()`
     * e `preEfetivar()`.
     *
     * `transactionId` é o identificador do cliente para a transação — use um
     * valor estável por pagamento para conseguir rastrear/anular depois.
     * `indicadorFuncao`: 1 = pagamento/agendamento, 2 = anulação.
     *
     * @param  array<string, mixed>  $dados  Obrigatórios: codigoAgencia, formaCaptura,
     *                                       codigoBarras, dataVencimento, valorTitulo, dataMovimento, dataPagamento,
     *                                       horaTransacao, formaPagamento, bancoDebito, agenciaDebito, contaDebito,
     *                                       transactionId, cnpjCpfPtdor, filialCnpjPtdor, ctrlCnpjPtdor.
     */
    public function efetivar(array $dados): EfetivacaoPagamento
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_EFETIVACAO, body: $dados);

        return EfetivacaoPagamento::fromArray($data);
    }

    /**
     * Lista agendamentos e pagamentos no período. Paginação por restart:
     * reenvie `restartSaida` em `restartEntrada` enquanto houver mais páginas.
     *
     * @param  array{versaoLayout: int, bancoDaContaDebito: int, agenciaDaContaDebito: int, contaCorrenteDebito: int, dataInicial: string, dataFinal: string, situacaoPagamento: int, restartEntrada?: string}  $filtros
     */
    public function listarAgendamentos(array $filtros): ListaAgendamentos
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_LISTA_AGENDAMENTOS, body: $filtros);

        return ListaAgendamentos::fromArray($data);
    }

    /**
     * Altera um agendamento (data, valor e/ou forma de pagamento).
     * Informe `numeroProtocolo` OU `idTransacao` — um dos dois é obrigatório.
     *
     * @param  array<string, mixed>  $dados  Obrigatórios: versao (fixo 2), bancoDebito,
     *                                       agenciaDebito, digitoAgencia, contaDebito, digitoConta, dataPagamento,
     *                                       valorPagamento + (numeroProtocolo | idTransacao).
     */
    public function alterarAgendamento(array $dados): AlteracaoAgendamento
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_ALTERA_AGENDAMENTO, body: $dados);

        return AlteracaoAgendamento::fromArray($data);
    }

    /**
     * Exclui (cancela) um agendamento ainda não efetivado.
     * Informe `numeroProtocolo` OU `idTransacao` — um dos dois é obrigatório.
     *
     * @param  array{versao?: int, bancoDebito?: int, agenciaDebito?: int, digitoAgencia?: int, contaDebito?: int, digitoConta?: string, numeroProtocolo?: int, idTransacao?: int}  $dados
     */
    public function excluirAgendamento(array $dados): ExclusaoAgendamento
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_EXCLUI_AGENDAMENTO, body: $dados);

        return ExclusaoAgendamento::fromArray($data);
    }

    /**
     * Consulta de um pagamento específico (por protocolo ou transactionId).
     *
     * @param  array{versaoLayout: string, numeroBanco: string, numeroAgencia: string, numeroConta: string, numeroProtocolo?: string, dataPagamento?: string, transactionId?: string}  $filtros
     */
    public function consultarPagamento(array $filtros): PagamentoEspecifico
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_CONSULTA_PAGAMENTO, body: $filtros);

        return PagamentoEspecifico::fromArray($data);
    }

    /**
     * Lista os pagamentos DEVOLVIDOS (débitos que não se efetivaram) no
     * período. Mesma paginação por restart da lista de agendamentos.
     *
     * @param  array{versaoLayout: int, bancoDebito: int, agenciaDebito: int, contaDebito: int, dataDevolucaoInicial?: string, dataDevolucaoFinal?: string, restartEntrada?: string}  $filtros
     */
    public function listarDevolvidos(array $filtros): ListaPagamentosDevolvidos
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_LISTA_DEVOLVIDOS, body: $filtros);

        return ListaPagamentosDevolvidos::fromArray($data);
    }

    /**
     * Contrato cross-bank PaymentsEndpoint: efetiva o pagamento do boleto.
     * ATENÇÃO — este é o passo que DEBITA. O fluxo completo do Bradesco é
     * consultarParametros -> validarTitulo -> preEfetivar -> efetivar; use os
     * métodos nomeados quando precisar das etapas anteriores.
     *
     * @param  array<string, mixed>  $dados
     */
    public function pagarBoleto(array $dados): DTOInterface
    {
        return $this->efetivar($dados);
    }

    /**
     * Contrato cross-bank PaymentsEndpoint: consulta um pagamento pelo
     * protocolo devolvido na efetivação.
     */
    public function consultar(string $identificador): DTOInterface
    {
        return $this->consultarPagamento(['numeroProtocolo' => $identificador]);
    }
}
