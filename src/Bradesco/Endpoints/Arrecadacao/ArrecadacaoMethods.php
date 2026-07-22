<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Arrecadacao;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao\ConsultaPagamentosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\Arrecadacao\PagamentoEfetivacaoResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Pagamento de contas de consumo e tributos (arrecadação via código de barras)
 * — Bradesco.
 *
 * É o produto que permite pagar água/luz/gás/telefone e tributos (DARF, GARE,
 * DAS, IPVA, IPTU, FGTS, e-Social…) direto do contas a pagar do ERP: informa-se
 * o código de barras do documento e a conta a ser debitada.
 *
 * ⚠️ MOVIMENTA DINHEIRO. O fluxo do Bradesco é de DUAS CHAMADAS no MESMO
 * endpoint (POST .../pagamentoContaConsumo):
 *   1. `tipoRegistro = 0` (pré-confirmação) — o banco consiste a barra e
 *      devolve os dados do documento (valor, vencimento, empresa conveniada,
 *      encargos). NÃO debita.
 *   2. `tipoRegistro = 1` (inclusão/efetivação) — o pagamento é efetivado nas
 *      bases do banco e volta a `autenticacaoBancaria` do comprovante.
 * Use `preConfirmar()` antes de `efetivar()`; use o mesmo `idTransacao` nas
 * duas para conseguir rastrear depois na consulta.
 *
 * ## Campos de código de barras aceitos
 * - `codigoBarras` (obrigatório): a barra de ARRECADAÇÃO no padrão FEBRABAN,
 *   com **44 posições numéricas** (sem os dígitos verificadores de bloco).
 *   Exemplo da spec: `82680000000930700971493204696440151015721012`.
 *   Convenções da barra FEBRABAN de arrecadação:
 *     - posição 1 = identificação do produto, sempre `8`;
 *     - posição 2 = segmento (1 prefeituras, 2 saneamento, 3 energia/gás,
 *       4 telecomunicações, 5 órgãos governamentais, 6 carnês, 7 multas de
 *       trânsito, 9 uso exclusivo do banco);
 *     - posição 3 = identificação do valor efetivo/referência;
 *     - posição 4 = DV geral da barra;
 *     - posições 5–15 = valor; 16–19 = empresa/órgão; 20–44 = campo livre.
 *   Se o documento vier na **linha digitável de 48 dígitos** (4 blocos de 12,
 *   cada um com o próprio DV), remova o DV de cada bloco antes de enviar — o
 *   que resulta exatamente nas 44 posições.
 * - `valorPrincipal` (obrigatório): valor a debitar, `0000000000000.00`.
 *   Para barras com valor referência (posição 3 = 6/7/8/9) o valor é digitado;
 *   a pré-confirmação devolve `obrigaDigitarValorDebito` (`S`/`N`) dizendo se
 *   ele é obrigatório ou proibido.
 * - Na resposta: `codigoBarras` volta COM os dígitos verificadores, e a
 *   consulta traz também `codigoBarrasComDigito`, `campoDigitado1` e
 *   `campoDigitado2` (campos livres digitados, ex.: FGTS).
 *
 * Família de autorizador: OPEN_API (host openapi.bradesco.com.br) — o `servers`
 * da spec dos endpoints aponta para `openapisandbox.prebanco.com.br`, host da
 * família Open API. (O arquivo de token que acompanha a doc é o "OAuth Pix
 * MTLS" apontando para qrpix — inconsistência da documentação; vale o host dos
 * endpoints.)
 *
 * Base path: /pagamento/arrecadacao-via-codbarras/v1
 */
final class ArrecadacaoMethods extends BaseMethods
{
    private const BASE = '/pagamento/arrecadacao-via-codbarras/v1';

    private const PATH_PAGAMENTO = self::BASE.'/pagamentoContaConsumo';

    /**
     * Pré-confirmação (tipoRegistro = 0): consiste o código de barras e devolve
     * os dados do documento. NÃO debita a conta.
     *
     * @param  array{agencia: int, conta: int, codigoBarras: string, dataDebito: string, valorPrincipal: float|int, digitoAgencia?: int, digitoConta?: string, tipoConta?: int, agenciaTerminal?: int, descricaoCliente?: string, idTransacao?: string, tipoRegistro?: int}  $dados
     */
    public function preConfirmar(array $dados): PagamentoEfetivacaoResponse
    {
        return $this->pagar(array_merge($dados, ['tipoRegistro' => 0]));
    }

    /**
     * Efetivação (tipoRegistro = 1): EFETIVA o pagamento — o dinheiro sai da
     * conta. Só chame depois de uma pré-confirmação conferida.
     *
     * @param  array{agencia: int, conta: int, codigoBarras: string, dataDebito: string, valorPrincipal: float|int, digitoAgencia?: int, digitoConta?: string, tipoConta?: int, agenciaTerminal?: int, descricaoCliente?: string, idTransacao?: string, tipoRegistro?: int}  $dados
     */
    public function efetivar(array $dados): PagamentoEfetivacaoResponse
    {
        return $this->pagar(array_merge($dados, ['tipoRegistro' => 1]));
    }

    /**
     * POST /pagamento/arrecadacao-via-codbarras/v1/pagamentoContaConsumo
     *
     * Envia o payload EXATAMENTE como recebido — o `tipoRegistro` (0 consulta /
     * 1 inclusão) fica por conta de quem chama. Prefira `preConfirmar()` e
     * `efetivar()`, que são explícitos sobre o que cada chamada faz.
     *
     * @param  array<string, mixed>  $dados
     */
    public function pagar(array $dados): PagamentoEfetivacaoResponse
    {
        $data = $this->makeRequest(HttpMethod::POST, self::PATH_PAGAMENTO, body: $dados);

        return PagamentoEfetivacaoResponse::fromArray($data);
    }

    /**
     * GET /pagamento/arrecadacao-via-codbarras/v1/{agencia}/{conta}/{tipoConta}
     *
     * Consulta os pagamentos de arrecadação efetuados na conta. A API devolve
     * uma LISTA de blocos; cada bloco traz até 5 registros em `regSaida` e o
     * controle de paginação em `restart` (1 = existem mais dados) / `contr`
     * (quantas consultas já foram feitas — primeiro envio = 0).
     *
     * @param  int  $tipoConta  1 corrente, 2 poupança, 5 corrente/poupança
     * @param  int  $tipoConsulta  01 alterar, 02 remover, 03 consulta obrigada efetuada,
     *                             04 agendamento de débito, 05 débito não efetuado,
     *                             06 pedido de pagamento on-line, 07 pagamentos só com código de barras
     * @param  int  $segmentoConsulta  01 imposto municipal, 02 saneamento, 03 eletricidade,
     *                                 04 telefone, 30 gás natural, 50 imposto federal, 10 outros,
     *                                 90 todas as contas de consumo, 91 segmento 10 (impostos),
     *                                 99 todos os registros da conta
     * @param  string  $dataInicial  AAAA-MM-DD
     * @param  string  $dataFinal  AAAA-MM-DD
     * @param  string|null  $idTransacao  identificação única enviada na efetivação
     * @return array<int, ConsultaPagamentosResponse>
     */
    public function consultar(
        int $agencia,
        int $conta,
        int $tipoConta,
        int $tipoConsulta,
        int $segmentoConsulta,
        string $dataInicial,
        string $dataFinal,
        ?string $idTransacao = null,
    ): array {
        $query = [
            'tipoConsulta' => $tipoConsulta,
            'segmentoConsulta' => $segmentoConsulta,
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
        ];

        if ($idTransacao !== null) {
            $query['idTransacao'] = $idTransacao;
        }

        $path = self::BASE.'/'.$agencia.'/'.$conta.'/'.$tipoConta;

        $data = $this->makeRequest(HttpMethod::GET, $path, query: $query);

        // A resposta de sucesso é um array de blocos; toleramos um bloco único.
        $blocos = array_is_list($data) ? $data : [$data];

        return array_map(
            static fn (array $bloco): ConsultaPagamentosResponse => ConsultaPagamentosResponse::fromArray($bloco),
            $blocos,
        );
    }
}
