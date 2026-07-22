<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\DebitoVeicular;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\MgComprovanteDetalhadoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\MgEfetuaPagamentoResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\MgListaComprovantesResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\MgListaDebitosResponse;
use SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular\MgObtemGuiaResponse;
use SistemAtc\Banks\Common\Enums\HttpMethod;

/**
 * Débito Veicular — MINAS GERAIS (SEFAZ-MG) — Bradesco.
 *
 * Consulta e pagamento de IPVA, licenciamento e demais débitos veiculares de
 * veículos emplacados em MG, com débito em conta corrente.
 *
 * ## Fluxo
 *   1. `listarDebitos()` — obrigatória. Devolve os débitos em aberto e, junto,
 *      o `controleSessao` da consulta e o `identificadorDebito` de cada débito.
 *   2. `obterGuia()` — opcional. Gera a GUIA (DAE) de UM débito e devolve o
 *      `codigoBarras`. Não debita nada.
 *   3. `efetuarPagamento()` — ⚠️ debita a conta.
 *   4. `listarComprovantes()` / `consultarComprovante()` — 2ª via.
 *
 * ⚠️ Restrição do órgão (documentada na spec do `obtem-guia`): SÓ é possível
 * pagar UM débito por vez — não há pagamento em lote em MG.
 *
 * ## Consulta x pagamento
 * CONSULTAM: listarDebitos, obterGuia, listarComprovantes,
 * consultarComprovante.
 * ⚠️ DEBITA A CONTA: efetuarPagamento.
 *
 * ## Identificação / idempotência
 * Não há chave de idempotência dedicada. Fazem esse papel:
 *   - `controleSessao` (string): identificador da SESSÃO devolvido por
 *     `listarDebitos()`; precisa ser repassado sem alteração em `obterGuia()` e
 *     em `efetuarPagamento()`. É o que amarra consulta e pagamento.
 *   - `identificadorDebito` (int): identifica o débito específico dentro da
 *     sessão; sai de `listarDebitos()` e entra no pagamento, na guia e na
 *     consulta de comprovante.
 *   - `codigoBarras` (string): a guia efetivamente paga.
 *   - `autenticacaoBancaria`: devolvido pelo pagamento — é o número do
 *     comprovante e a chave de `consultarComprovante()`.
 * Em TIMEOUT: consulte com `listarComprovantes()` antes de reenviar.
 *
 * ⚠️ Regra geral do Bradesco (ver `BaseMethods`): erro de negócio vem com HTTP
 * 200. Só dê o pagamento por concluído com `codigoMensagem` = 'LCBR0000' /
 * `descricaoMensagem` de sucesso e `autenticacaoBancaria` preenchida.
 *
 * Família de autorizador: OPEN_API (host openapi.bradesco.com.br).
 *
 * Base path: /v1/debitos-veiculares-mg
 */
final class DebitoVeicularMgMethods extends BaseMethods
{
    private const BASE = '/v1/debitos-veiculares-mg';

    private const PATH_DEBITOS = self::BASE.'/lista-debitos/listaDebitosPendentesMG';

    private const PATH_GUIA = self::BASE.'/obtem-guia/obtemGuiaMG';

    private const PATH_PAGAMENTO = self::BASE.'/efetua-pagamento/efetuaPagamentoMG';

    private const PATH_COMPROVANTES = self::BASE.'/lista-comprovantes/listaComprovantesMG';

    private const PATH_COMPROVANTE_DET = self::BASE.'/lista-comprovante-detalhada/consultaComprovanteMG';

    /**
     * CONSULTA. Débitos veiculares pendentes de um RENAVAM em MG. Obrigatória
     * antes de qualquer pagamento: é ela que devolve `controleSessao` e os
     * `identificadorDebito` de `debitosListagem`.
     *
     * POST /v1/debitos-veiculares-mg/lista-debitos/listaDebitosPendentesMG
     *
     * @param  array{codigoRenavam: int, codigoConta: int, codigoCanal: int, codigoAgencia: int}  $dados
     */
    public function listarDebitos(array $dados): MgListaDebitosResponse
    {
        return MgListaDebitosResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_DEBITOS, body: $dados)
        );
    }

    /**
     * CONSULTA. Gera a guia (DAE) de UM débito e devolve o `codigoBarras`
     * correspondente. NÃO debita nada — é só a emissão. Por determinação do
     * órgão, um débito por vez.
     *
     * Repasse aqui o `identificadorDebito` e o `controleSessao` vindos de
     * `listarDebitos()`.
     *
     * POST /v1/debitos-veiculares-mg/obtem-guia/obtemGuiaMG
     *
     * @param  array{codigoRenavam: int, identificadorDebito: int, codigoConta: int, codigoCanal: int, codigoAgencia: int, tipoConta: string, controleSessao: string}  $dados
     */
    public function obterGuia(array $dados): MgObtemGuiaResponse
    {
        return MgObtemGuiaResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_GUIA, body: $dados)
        );
    }

    /**
     * ⚠️ MOVIMENTA DINHEIRO. Paga UM débito veicular de MG, debitando a conta
     * informada.
     *
     * Campos de identificação/idempotência (todos obrigatórios pela spec):
     *   - `controleSessao` (string): a sessão devolvida por `listarDebitos()`.
     *     É o vínculo entre a consulta e este pagamento — não invente e não
     *     reaproveite de outra consulta.
     *   - `identificadorDebito` (int): o débito escolhido.
     *   - `codigoBarras` (string): a guia (de `listarDebitos()`/`obterGuia()`).
     *   - `valorPagamento` (float) e `dataDebito` (`DD/MM/AAAA`).
     *   - `meioAutenticacao`, `dispositivoSeguranca`, `operacaoLynx`: campos de
     *     canal do Bradesco; a spec exemplifica todos vazios ('').
     * A resposta traz `autenticacaoBancaria` — guarde-a, é o comprovante.
     *
     * Em timeout, NÃO reenvie: chame `listarComprovantes()` para ver se o
     * pagamento entrou.
     *
     * POST /v1/debitos-veiculares-mg/efetua-pagamento/efetuaPagamentoMG
     *
     * @param  array{dataDebito: string, codigoRenavam: int, identificadorDebito: int, codigoCanal: int, meioAutenticacao: string, controleSessao: string, dispositivoSeguranca: string, codigoConta: int, operacaoLynx: string, codigoBarras: string, codigoAgencia: int, tipoConta: string, valorPagamento: float}  $dados
     */
    public function efetuarPagamento(array $dados): MgEfetuaPagamentoResponse
    {
        return MgEfetuaPagamentoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_PAGAMENTO, body: $dados)
        );
    }

    /**
     * CONSULTA. Comprovantes (resumidos) dos pagamentos de um RENAVAM em MG.
     * Cada item traz `autenticacaoBancaria` e `identificadorDebito`, exigidos
     * por `consultarComprovante()`.
     *
     * POST /v1/debitos-veiculares-mg/lista-comprovantes/listaComprovantesMG
     *
     * @param  array{codigoRenavam: int, codigoConta: int, codigoAgencia: int}  $dados
     */
    public function listarComprovantes(array $dados): MgListaComprovantesResponse
    {
        return MgListaComprovantesResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_COMPROVANTES, body: $dados)
        );
    }

    /**
     * CONSULTA (2ª via). Comprovante DETALHADO de um pagamento, localizado pelo
     * par `identificadorDebito` + `autenticacaoBancaria`. `codigoBarras` é
     * opcional na spec.
     *
     * POST /v1/debitos-veiculares-mg/lista-comprovante-detalhada/consultaComprovanteMG
     *
     * @param  array{codigoRenavam: int, identificadorDebito: int, codigoConta: int, codigoAgencia: int, autenticacaoBancaria: int, codigoBarras?: string}  $dados
     */
    public function consultarComprovante(array $dados): MgComprovanteDetalhadoResponse
    {
        return MgComprovanteDetalhadoResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, self::PATH_COMPROVANTE_DET, body: $dados)
        );
    }
}
