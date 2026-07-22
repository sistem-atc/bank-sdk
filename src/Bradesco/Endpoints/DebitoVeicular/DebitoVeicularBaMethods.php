<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\BaComprovanteDetalhadoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\BaComprovanteResumidoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\BaEfetuaPagamentoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\BaListaDebitosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\BaTiposDebitosResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Débito Veicular — BAHIA (DETRAN-BA / SEFAZ-BA) — Bradesco.
 *
 * Consulta e pagamento de IPVA, taxa de licenciamento, DPVAT e multas de
 * veículos emplacados na BA, com débito em conta corrente.
 *
 * ## Fluxo
 *   1. `listarTiposDebitos()` — opcional. Tabela de `codigoDebito`
 *      (= `codigoPagamento` das listagens): licenciamento cota única, IPVA,
 *      multa etc.
 *   2. Uma das TRÊS listagens (obrigatória antes do pagamento) — mudam só o
 *      critério de busca, o envelope de resposta é o mesmo:
 *        - `listarDebitosPorRenavam()` — todos os débitos do RENAVAM;
 *        - `listarDebitosPorAno()`     — filtra por `anoExercicio`;
 *        - `listarDebitosPorMulta()`   — filtra por `numeroMulta`.
 *   3. `efetuarPagamento()` — ⚠️ debita a conta. A spec descreve a operação em
 *      DUAS ETAPAS ("consistência do pagamento e depois efetivação"),
 *      controladas por `codigoFuncao` (1 caractere; exemplo da spec: 'P').
 *   4. `listarComprovantes()` → `consultarComprovante()` — 2ª via.
 *
 * ## Consulta x pagamento
 * CONSULTAM: listarTiposDebitos, listarDebitosPorRenavam,
 * listarDebitosPorAno, listarDebitosPorMulta, listarComprovantes,
 * consultarComprovante.
 * ⚠️ DEBITA A CONTA: efetuarPagamento.
 *
 * ## Identificação / idempotência
 * Não há chave de idempotência dedicada. Fazem esse papel:
 *   - `nsuBanco` / `nsuOrigem` (int): NSU do lançamento. A listagem devolve
 *     `nsuBanco`/`nsuOrigem`; o pagamento reenvia como `nsuOrigem` e a
 *     consulta detalhada como `nsuBanco`. É o identificador de rastreio.
 *   - `nsuProdeb` (na resposta): NSU do lado da PRODEB/DETRAN-BA.
 *   - `codigoFuncao` (1 char): seleciona consistência x efetivação.
 *   - `codigoPagamento` + `codigoRenavam` (+ `numeroMulta`, `numeroParcela`,
 *     `anoExercicio`): identificam O QUE está sendo pago.
 * Em TIMEOUT: chame `listarComprovantes()` antes de reenviar.
 *
 * ⚠️ ARMADILHA DE GRAFIA (da própria spec, respeitada aqui):
 *   - o path da listagem por RENAVAM é `/detran/lista-debitos/renavan`
 *     (com "n" no fim);
 *   - `listarComprovantes()` recebe o campo `codigoRenavan` (com "n"),
 *     enquanto TODOS os outros endpoints usam `codigoRenavam` (com "m");
 *   - as listagens usam `validacaoListaPositiva` (L maiúsculo) e o pagamento
 *     usa `validacaolistaPositiva` (l minúsculo).
 *   Não "corrija" — é assim que o host espera.
 *
 * ⚠️ Regra geral do Bradesco (ver `BaseMethods`): erro de negócio vem com HTTP
 * 200. Confira `codigoRetorno` + `statusPagamento` + `codigoMensagem` antes de
 * dar o pagamento por concluído.
 *
 * Família de autorizador: OPEN_API (host openapi.bradesco.com.br).
 *
 * Base path: /v1/debitos-veiculares-ba
 */
final class DebitoVeicularBaMethods extends BaseMethods
{
    private const BASE = '/v1/debitos-veiculares-ba';

    private const PATH_TIPOS = self::BASE.'/detran/listaTiposDebitos';

    private const PATH_DEBITOS_RENAVAM = self::BASE.'/detran/lista-debitos/renavan';

    private const PATH_DEBITOS_ANO = self::BASE.'/detran/lista-debitos/ano';

    private const PATH_DEBITOS_MULTA = self::BASE.'/detran/lista-debitos/multa';

    private const PATH_PAGAMENTO = self::BASE.'/renavam/efetua-pagamento/efetuaPagamentoBA';

    private const PATH_COMPROVANTES = self::BASE.'/renavam/lista-comprovantes/consulta/resumida';

    private const PATH_COMPROVANTE_DET = self::BASE.'/renavam/lista-comprovante-detalhada/listaComprovanteDetalheBa';

    /**
     * CONSULTA. Tipos de débito do DETRAN-BA — devolve os `codigoDebito` que
     * alimentam o `codigoPagamento` das listagens.
     *
     * `codigoConvenio` e `codigoSegmento` são opcionais na spec.
     *
     * POST /v1/debitos-veiculares-ba/detran/listaTiposDebitos
     *
     * @param  array{codigoServico: int, codigoConta: int, codigoCanal: int, codigoAgencia: int, codigoUF: string, codigoConvenio?: int, codigoSegmento?: int}  $dados
     */
    public function listarTiposDebitos(array $dados): BaTiposDebitosResponse
    {
        return BaTiposDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_TIPOS, body: $dados)
        );
    }

    /**
     * CONSULTA. Débitos em aberto de um RENAVAM na BA.
     *
     * ⚠️ O path do Bradesco é `/renavan` (com "n" no fim) — grafia da spec.
     *
     * POST /v1/debitos-veiculares-ba/detran/lista-debitos/renavan
     *
     * @param  array{codigoRenavam: int, codigoPagamento: int, codigoBanco: int, codigoConta: int, codigoCanal: int, codigoAgencia: int, validacaoListaPositiva: string}  $dados
     */
    public function listarDebitosPorRenavam(array $dados): BaListaDebitosResponse
    {
        return BaListaDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_DEBITOS_RENAVAM, body: $dados)
        );
    }

    /**
     * CONSULTA. Débitos em aberto de um RENAVAM filtrados por ano de exercício.
     * Mesmo envelope de resposta da consulta por RENAVAM.
     *
     * POST /v1/debitos-veiculares-ba/detran/lista-debitos/ano
     *
     * @param  array{codigoRenavam: int, codigoPagamento: int, codigoBanco: int, codigoConta: int, codigoCanal: int, anoExercicio: int, codigoAgencia: int, validacaoListaPositiva: string}  $dados
     */
    public function listarDebitosPorAno(array $dados): BaListaDebitosResponse
    {
        return BaListaDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_DEBITOS_ANO, body: $dados)
        );
    }

    /**
     * CONSULTA. Débito de UMA multa específica (`numeroMulta`) de um RENAVAM.
     * Mesmo envelope de resposta das demais listagens.
     *
     * POST /v1/debitos-veiculares-ba/detran/lista-debitos/multa
     *
     * @param  array{codigoRenavam: int, codigoPagamento: int, numeroMulta: int, codigoBanco: int, codigoConta: int, codigoCanal: int, codigoAgencia: int, validacaoListaPositiva: string}  $dados
     */
    public function listarDebitosPorMulta(array $dados): BaListaDebitosResponse
    {
        return BaListaDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_DEBITOS_MULTA, body: $dados)
        );
    }

    /**
     * ⚠️ MOVIMENTA DINHEIRO. Consiste e efetua o pagamento de débito veicular
     * da BA, debitando a conta informada.
     *
     * Campos de identificação/idempotência (todos obrigatórios pela spec):
     *   - `codigoFuncao` (1 char): seleciona a etapa (consistência x
     *     efetivação) da operação em duas etapas da BA. Exemplo da spec: 'P'.
     *   - `nsuOrigem` (int): NSU do lançamento — é o `nsuBanco`/`nsuOrigem`
     *     devolvido pela listagem de débitos. Identificador de rastreio; volta
     *     na resposta junto de `nsuProdeb`.
     *   - `codigoPagamento` + `codigoRenavam` (+ `numeroMulta`,
     *     `numeroParcela`, `anoExercicio`, `anoCrvl`): identificam o débito.
     *   - `cpfCnpjPrincipal`/`cpfCnpjFilial`/`cpfCnpjDigito`: contribuinte.
     *   - `conexao`, `sequencialPeriferico`, `identificacaoPeriferico`,
     *     `identificacaoLuResposta`, `caracteristicaOperLynx`: identificação do
     *     terminal/canal exigida pelo host.
     *   - os valores (`valorTotal`, `valorTotalMulta`,
     *     `valorDespesaOperacional`, `valorTarifaPostagem`,
     *     `valorTarifaBancaria`) devem sair TAL E QUAL da listagem.
     *   - `validacaolistaPositiva` — atenção: aqui é com 'l' MINÚSCULO.
     *
     * Em timeout, NÃO reenvie: use `listarComprovantes()` para conferir.
     *
     * POST /v1/debitos-veiculares-ba/renavam/efetua-pagamento/efetuaPagamentoBA
     *
     * @param  array{cpfCnpjFilial: int, codigoRenavam: int, numeroMulta: int, localEntrega: int, codigoFuncao: string, anoCrvl: int, sequencialPeriferico: string, conexao: string, identificacaoLuResposta: int, digitoConta: int, nsuOrigem: int, valorTotal: float, codigoConta: int, numeroParcela: int, cpfCnpjDigito: int, validacaolistaPositiva: string, valorDespesaOperacional: float, identificacaoPeriferico: string, valorTarifaPostagem: float, codigoCanal: int, valorTotalMulta: float, caracteristicaOperLynx: string, anoExercicio: int, codigoPlaca: string, valorTarifaBancaria: float, codigoPagamento: int, cpfCnpjPrincipal: int, codigoMunicipio: int, codigoAgencia: int, tipoConta: string}  $dados
     */
    public function efetuarPagamento(array $dados): BaEfetuaPagamentoResponse
    {
        return BaEfetuaPagamentoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_PAGAMENTO, body: $dados)
        );
    }

    /**
     * CONSULTA. Comprovantes (resumidos) dos pagamentos de um RENAVAM num ano.
     * Obrigatória antes da consulta detalhada: devolve `dataPagamento`
     * (`DD.MM.AAAA`), `codigoPagamento` e `nsuBanco` de cada item.
     *
     * ⚠️ Este é o ÚNICO endpoint da BA cujo campo de RENAVAM se chama
     * `codigoRenavan` (com "n") — grafia da spec.
     *
     * POST /v1/debitos-veiculares-ba/renavam/lista-comprovantes/consulta/resumida
     *
     * @param  array{codigoRenavan: int, codigoBanco: int, codigoConta: int, anoExercicio: int, codigoUF: string, codigoAgencia: int}  $dados
     */
    public function listarComprovantes(array $dados): BaComprovanteResumidoResponse
    {
        return BaComprovanteResumidoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_COMPROVANTES, body: $dados)
        );
    }

    /**
     * CONSULTA (2ª via). Comprovante DETALHADO de um pagamento da BA,
     * localizado por RENAVAM + `codigoPagamento` + `anoExercicio` +
     * `dataPagamento` (`DD.MM.AAAA`) + `nsuBanco`, todos vindos de
     * `listarComprovantes()`. `origemCompra` é opcional.
     *
     * POST /v1/debitos-veiculares-ba/renavam/lista-comprovante-detalhada/listaComprovanteDetalheBa
     *
     * @param  array{dataPagamento: string, codigoRenavam: int, codigoPagamento: int, codigoBanco: int, codigoConta: int, codigoCanal: int, anoExercicio: int, codigoUF: string, codigoAgencia: int, nsuBanco: int, origemCompra?: int}  $dados
     */
    public function consultarComprovante(array $dados): BaComprovanteDetalhadoResponse
    {
        return BaComprovanteDetalhadoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_COMPROVANTE_DET, body: $dados)
        );
    }
}
