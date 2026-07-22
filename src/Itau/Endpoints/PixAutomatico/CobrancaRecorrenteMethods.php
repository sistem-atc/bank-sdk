<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\PixAutomatico;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\CobrancaRecorrente;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\CobrancaRecorrenteList;
use SistemAtc\Banks\Itau\DTO\Response\PixAutomatico\WebhookConfig;

/**
 * API Pix Automático (recebimentos) — gestão de COBRANÇAS RECORRENTES (CobR),
 * o agendamento de cada débito sob um contrato de recorrência. Cobre os grupos
 * `/cobr` e `/webhookcobr` (webhook de cobrança).
 *
 * Host de produção dedicado: `https://pixautomatico-recebimentos.api.itau.com`.
 */
final class CobrancaRecorrenteMethods extends BaseMethods
{
    private const COBR = '/cobr';

    private const WEBHOOK = '/webhookcobr';

    // ---- Cobrança recorrente (/cobr) -----------------------------------

    /**
     * Criar cobrança recorrente — POST /cobr (txid gerado pelo PSP).
     *
     * @param  array<string, mixed>  $dados
     */
    public function criar(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::POST, self::COBR, body: $dados);

        return CobrancaRecorrente::fromArray($data);
    }

    /**
     * Criar cobrança recorrente informando o txid — PUT /cobr/{txid}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function criarComTxid(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::COBR.'/'.rawurlencode($txid), body: $dados);

        return CobrancaRecorrente::fromArray($data);
    }

    /**
     * Consultar cobrança recorrente por txid — GET /cobr/{txid}.
     */
    public function consultar(string $txid): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::COBR.'/'.rawurlencode($txid));

        return CobrancaRecorrente::fromArray($data);
    }

    /**
     * Revisar (cancelar / alterar) cobrança recorrente — PATCH /cobr/{txid}.
     *
     * @param  array<string, mixed>  $dados
     */
    public function revisar(string $txid, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PATCH, self::COBR.'/'.rawurlencode($txid), body: $dados);

        return CobrancaRecorrente::fromArray($data);
    }

    /**
     * Consultar cobranças recorrentes — GET /cobr (lista na chave `cobsr`).
     *
     * @param  array<string, mixed>  $filtros  inicio/fim/idRec (obrigatórios), cpf/cnpj/status
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::COBR, query: $filtros);

        return CobrancaRecorrenteList::fromArray($data);
    }

    /**
     * Solicitar retentativa de liquidação numa data —
     * POST /cobr/{txid}/retentativa/{data} (data no formato YYYY-MM-DD).
     */
    public function solicitarRetentativa(string $txid, string $data): DTOInterface
    {
        $payload = $this->makeRequest(
            HttpMethod::POST,
            self::COBR.'/'.rawurlencode($txid).'/retentativa/'.rawurlencode($data),
        );

        return CobrancaRecorrente::fromArray($payload);
    }

    // ---- Webhook de cobrança (/webhookcobr) ----------------------------

    /**
     * Configurar webhook de cobrança recorrente — PUT /webhookcobr.
     *
     * @param  array<string, mixed>  $dados  ex.: ['webhookUrl' => '...']
     */
    public function configurarWebhook(array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::WEBHOOK, body: $dados);

        return WebhookConfig::fromArray($data);
    }

    /**
     * Consultar webhook de cobrança cadastrado — GET /webhookcobr.
     */
    public function consultarWebhook(): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::WEBHOOK);

        return WebhookConfig::fromArray($data);
    }

    /**
     * Cancelar webhook de cobrança — DELETE /webhookcobr.
     */
    public function cancelarWebhook(): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::DELETE, self::WEBHOOK);

        return WebhookConfig::fromArray($data);
    }
}
