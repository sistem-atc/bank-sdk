<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\PixAutomatico;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\DadosPagador;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\LocationRecList;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\Loc;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\Recorrencia;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\RecorrenciaList;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\WebhookConfig;

/**
 * API Pix Automático (recebimentos) — gestão de RECORRÊNCIAS/autorização.
 * Cobre os grupos `/rec` (contrato de recorrência), `/solicrec` (solicitação de
 * aceite do pagador), `/locrec` (locations do payload de recorrência) e
 * `/webhookrec` (webhook de recorrência).
 *
 * Host de produção dedicado: `https://pixautomatico-recebimentos.api.itau.com`,
 * com prefixo de versão `/pixautomatico/v1` em TODOS os paths (conforme a
 * collection `ng7_api_pixautomatico_v1`, baseUrl `.../pixautomatico/v1`).
 */
final class RecorrenciaMethods extends BaseMethods
{
    private const PREFIX = '/pixautomatico/v1';

    private const REC = self::PREFIX.'/rec';

    /** Os dados do pagador vivem sob `/recorrencia`, não `/rec`. */
    private const RECORRENCIA = self::PREFIX.'/recorrencia';

    private const SOLICREC = self::PREFIX.'/solicrec';

    private const LOCREC = self::PREFIX.'/locrec';

    private const WEBHOOK = self::PREFIX.'/webhookrec';

    // ---- Recorrência (/rec) --------------------------------------------

    /**
     * Criar recorrência — POST /rec.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::REC, body: $dados);

        return Recorrencia::fromArray($data);
    }

    /**
     * Consultar lista de recorrências — GET /rec.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::REC, query: $filtros);

        return RecorrenciaList::fromArray($data);
    }

    /**
     * Consultar recorrência — GET /rec/{idRec}.
     *
     * @param  array<string, mixed>  $query
     */
    public function consultar(string $idRec, array $query = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::REC.'/'.rawurlencode($idRec), query: $query);

        return Recorrencia::fromArray($data);
    }

    /**
     * Revisar recorrência (cancelar / alterar data inicial / txid / pagador) —
     * PATCH /rec/{idRec}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function revisar(string $idRec, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PATCH, self::REC.'/'.rawurlencode($idRec), body: $dados);

        return Recorrencia::fromArray($data);
    }

    /**
     * Consultar dados do pagador da recorrência —
     * GET /recorrencia/{idRec}/dados-pagador.
     */
    public function consultarDadosPagador(string $idRec): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::RECORRENCIA.'/'.rawurlencode($idRec).'/dados-pagador');

        return DadosPagador::fromArray($data);
    }

    /**
     * Resolver o payload de recorrência a partir do token de acesso da URL do QR
     * (endpoint público, sem mTLS/OAuth) —
     * GET /{endpointOpcional}/token/rec/{recUrlAccessToken}.
     *
     * @param  string  $recUrlAccessToken  token que vem na URL do QR de recorrência
     * @param  string  $endpointOpcional  segmento opcional exigido por alguns PSPs (vazio por padrão)
     * @return array<string, mixed>  payload cru da recorrência
     */
    public function resolverPayloadPorToken(string $recUrlAccessToken, string $endpointOpcional = ''): array
    {
        $prefixoOpcional = $endpointOpcional === '' ? '' : '/'.rawurlencode($endpointOpcional);

        return $this->makeRequest(
            HttpMethod::GET,
            self::PREFIX.$prefixoOpcional.'/token/rec/'.rawurlencode($recUrlAccessToken),
        );
    }

    // ---- Solicitação de recorrência (/solicrec) ------------------------

    /**
     * Criar solicitação de confirmação de recorrência — POST /solicrec.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarSolicitacao(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::SOLICREC, body: $dados);

        return \SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\SolicitacaoRecorrencia::fromArray($data);
    }

    /**
     * Consultar solicitação de confirmação — GET /solicrec/{idSolicRec}.
     */
    public function consultarSolicitacao(string $idSolicRec): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::SOLICREC.'/'.rawurlencode($idSolicRec));

        return \SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\SolicitacaoRecorrencia::fromArray($data);
    }

    /**
     * Revisar (cancelar) solicitação de confirmação —
     * PATCH /solicrec/{idSolicRec}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function revisarSolicitacao(string $idSolicRec, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PATCH, self::SOLICREC.'/'.rawurlencode($idSolicRec), body: $dados);

        return \SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\SolicitacaoRecorrencia::fromArray($data);
    }

    // ---- Location de recorrência (/locrec) -----------------------------

    /**
     * Criar location do payload de recorrência — POST /locrec.
     */
    public function criarLocation(): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::LOCREC);

        return Loc::fromArray($data);
    }

    /**
     * Consultar locations cadastradas — GET /locrec.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listarLocations(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::LOCREC, query: $filtros);

        return LocationRecList::fromArray($data);
    }

    /**
     * Recuperar uma location — GET /locrec/{id}.
     */
    public function consultarLocation(string $id): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::LOCREC.'/'.rawurlencode($id));

        return Loc::fromArray($data);
    }

    /**
     * Desvincular a recorrência de uma location — DELETE /locrec/{id}/idRec.
     * O segmento `idRec` é LITERAL (não é o id da recorrência): remove o
     * vínculo rec↔location da location `{id}`.
     */
    public function desvincularLocation(string $id): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::DELETE,
            self::LOCREC.'/'.rawurlencode($id).'/idRec',
        );

        return Loc::fromArray($data);
    }

    // ---- Webhook de recorrência (/webhookrec) --------------------------

    /**
     * Configurar webhook de recorrência — PUT /webhookrec.
     *
     * @param  array<string, mixed>  $dados  ex.: ['webhookUrl' => '...']
     */
    public function configurarWebhook(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::WEBHOOK, body: $dados);

        return WebhookConfig::fromArray($data);
    }

    /**
     * Consultar webhook de recorrência cadastrado — GET /webhookrec.
     */
    public function consultarWebhook(): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::WEBHOOK);

        return WebhookConfig::fromArray($data);
    }

    /**
     * Cancelar webhook de recorrência — DELETE /webhookrec.
     */
    public function cancelarWebhook(): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::DELETE, self::WEBHOOK);

        return WebhookConfig::fromArray($data);
    }
}
