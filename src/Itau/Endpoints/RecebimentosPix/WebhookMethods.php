<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Itau\Bases\BaseMethods;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\Webhook;
use SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix\WebhookList;

/**
 * Notificações de recebimento via webhook — base `/regulatorio-pix/v2/webhook`.
 * O webhook é associado a uma chave Pix; o Itaú notifica a URL cadastrada
 * (acrescida do sufixo `/pix`) a cada recebimento.
 */
final class WebhookMethods extends BaseMethods
{
    private const BASE = '/regulatorio-pix/v2/webhook';

    /**
     * Cadastra ou atualiza o webhook de uma chave Pix (PUT /webhook/{chave}).
     * Body esperado: `{"webhookUrl": "https://..."}` (sem o sufixo /pix).
     *
     * @param array<string, mixed> $dados
     */
    public function cadastrar(string $chave, array $dados): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::PUT, self::BASE.'/'.rawurlencode($chave), body: $dados);

        return Webhook::fromArray($data);
    }

    /** Consulta o webhook cadastrado para uma chave específica (GET /webhook/{chave}). */
    public function consultar(string $chave): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE.'/'.rawurlencode($chave));

        return Webhook::fromArray($data);
    }

    /**
     * Lista todos os webhooks cadastrados (GET /webhook).
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::BASE, query: $filtros);

        return WebhookList::fromArray($data);
    }

    /** Cancela (exclui) o webhook de uma chave Pix (DELETE /webhook/{chave}). */
    public function cancelar(string $chave): void
    {
        $this->makeRequest(HttpMethod::DELETE, self::BASE.'/'.rawurlencode($chave));
    }
}
