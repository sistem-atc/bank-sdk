<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\PrComprovanteDetalhadoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\PrComprovanteResumidoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\PrEfetuaPagamentoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\PrListaDebitosResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Débito Veicular — PARANÁ (DETRAN-PR / SEFAZ-PR) — Bradesco.
 *
 * Consulta e pagamento de IPVA (cota única/parcelas), licenciamento e demais
 * tributos de veículos emplacados no PR, com débito em conta corrente.
 *
 * ## Fluxo
 *   1. `listarDebitos()` — obrigatória. Devolve os tributos em aberto em
 *      `lista` (codigoTributo, nomeTributo, descricaoTributo, anoExercicio,
 *      valorContaTributo) + `devedorPrincipal`, `codigoPlaca` e `nsuBanco`.
 *   2. `efetuarPagamento()` — ⚠️ debita a conta. A spec descreve a operação em
 *      DUAS ETAPAS ("consistência do pagamento e depois efetivação"),
 *      controladas pelo campo `codigoFuncao` (1 caractere; o exemplo da spec é
 *      'C', de consistência).
 *   3. `listarComprovantes()` → `consultarComprovante()` — 2ª via.
 *
 * ## Consulta x pagamento
 * CONSULTAM: listarDebitos, listarComprovantes, consultarComprovante.
 * ⚠️ DEBITA A CONTA: efetuarPagamento.
 *
 * ## Identificação / idempotência
 * Não há chave de idempotência dedicada. Fazem esse papel:
 *   - `nsuBanco` (int): NSU do lançamento. Vem de `listarDebitos()` e é
 *     reenviado no pagamento; volta na resposta. É o identificador de rastreio.
 *   - `codigoFuncao` (string, 1 char): decide se a chamada é a consistência ou
 *     a efetivação da mesma operação — leia a spec/manual do produto antes de
 *     mudar o valor.
 *   - `dataPagamento` (int, `AAAAMMDDHHMMSSN`): timestamp devolvido pela
 *     listagem de comprovantes (`dataHoraPagamento`) e exigido pela consulta
 *     detalhada; é o que identifica UM pagamento.
 *   - `codigoAutenticacao` (na resposta): autenticação bancária do comprovante.
 * Em TIMEOUT: chame `listarComprovantes()` antes de reenviar.
 *
 * ⚠️ Regra geral do Bradesco (ver `BaseMethods`): erro de negócio vem com HTTP
 * 200. Confira `codigoRetorno` (0 = ok) + `codigoMensagem` +
 * `codigoAutenticacao` antes de dar o pagamento por concluído.
 *
 * Família de autorizador: OPEN_API (host openapi.bradesco.com.br).
 *
 * Base path: /v1/debitos-veiculares-pr
 */
final class DebitoVeicularPrMethods extends BaseMethods
{
    private const BASE = '/v1/debitos-veiculares-pr';

    private const PATH_DEBITOS = self::BASE.'/lista-debitos/listaDebitoVeicularPR';

    private const PATH_PAGAMENTO = self::BASE.'/efetua-pagamento/efetuaPagamentoPR';

    private const PATH_COMPROVANTES = self::BASE.'/lista-comprovantes/listaComprovanteResumidaPr';

    private const PATH_COMPROVANTE_DET = self::BASE.'/lista-comprovante-detalhada/consultar';

    /**
     * CONSULTA. Débitos veiculares em aberto de um RENAVAM no PR.
     *
     * `validacaolistaPositiva` (grafia da spec, com 'l' minúsculo) é
     * obrigatório — 'N' no exemplo.
     *
     * POST /v1/debitos-veiculares-pr/lista-debitos/listaDebitoVeicularPR
     *
     * @param  array{codigoUF: string, codigoCanal: int, codigoAgencia: int, codigoConta: int, codigoRenavam: int, validacaolistaPositiva: string}  $dados
     */
    public function listarDebitos(array $dados): PrListaDebitosResponse
    {
        return PrListaDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_DEBITOS, body: $dados)
        );
    }

    /**
     * ⚠️ MOVIMENTA DINHEIRO. Consiste e efetua o pagamento de UM tributo
     * veicular do PR, debitando a conta informada.
     *
     * Campos de identificação/idempotência (todos obrigatórios pela spec):
     *   - `codigoFuncao` (1 char): seleciona a etapa (consistência x
     *     efetivação) da operação em duas etapas do PR. Exemplo da spec: 'C'.
     *   - `nsuBanco` (int, 12): NSU do lançamento, vindo de `listarDebitos()`.
     *     É o identificador de rastreio — volta na resposta.
     *   - `conexao`, `sequencialPeriferico`, `identificacaoPeriferico`,
     *     `identificacaoLuResposta`, `meioAutenticacao`: identificação do
     *     terminal/canal exigida pelo host do Bradesco.
     *   - `codigoTributo`, `nomeTributo`, `descricaoTributo`, `anoExercicio` e
     *     `valorContaTributo` devem sair TAL E QUAL do item de `lista` de
     *     `listarDebitos()`; `devedorPrincipal` e `codigoPlaca` idem.
     *   - `validacaolistaPositiva` (1 char), grafia da spec.
     *
     * Em timeout, NÃO reenvie: use `listarComprovantes()` para conferir.
     *
     * POST /v1/debitos-veiculares-pr/efetua-pagamento/efetuaPagamentoPR
     *
     * @param  array{codigoCanal: int, conexao: string, sequencialPeriferico: string, identificacaoPeriferico: string, identificacaoLuResposta: int, meioAutenticacao: string, codigoFuncao: string, codigoUF: string, codigoAgencia: int, codigoConta: int, digitoConta: int, codigoRenavam: int, nsuBanco: int, codigoPlaca: string, devedorPrincipal: string, codigoTributo: int, descricaoTributo: string, nomeTributo: string, anoExercicio: int, valorContaTributo: float, validacaolistaPositiva: string}  $dados
     */
    public function efetuarPagamento(array $dados): PrEfetuaPagamentoResponse
    {
        return PrEfetuaPagamentoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_PAGAMENTO, body: $dados)
        );
    }

    /**
     * CONSULTA. Comprovantes (resumidos) dos pagamentos de um RENAVAM num ano
     * de exercício. Obrigatória antes da consulta detalhada: é ela que devolve
     * `dataHoraPagamento` (o `dataPagamento` exigido lá).
     *
     * POST /v1/debitos-veiculares-pr/lista-comprovantes/listaComprovanteResumidaPr
     *
     * @param  array{codigoUF: string, codigoAgencia: int, codigoConta: int, codigoRenavam: int, codigoCanal: int, anoExercicio: int}  $dados
     */
    public function listarComprovantes(array $dados): PrComprovanteResumidoResponse
    {
        return PrComprovanteResumidoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_COMPROVANTES, body: $dados)
        );
    }

    /**
     * CONSULTA (2ª via). Comprovante DETALHADO de um pagamento do PR,
     * localizado por RENAVAM + `codigoTributo` + `anoExercicio` +
     * `dataPagamento` (o `dataHoraPagamento` no formato `AAAAMMDDHHMMSSN` que
     * `listarComprovantes()` devolve).
     *
     * POST /v1/debitos-veiculares-pr/lista-comprovante-detalhada/consultar
     *
     * @param  array{codigoRenavam: int, codigoUF: string, codigoAgencia: int, codigoConta: int, codigoCanal: int, codigoTributo: int, anoExercicio: int, dataPagamento: int}  $dados
     */
    public function consultarComprovante(array $dados): PrComprovanteDetalhadoResponse
    {
        return PrComprovanteDetalhadoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_COMPROVANTE_DET, body: $dados)
        );
    }
}
