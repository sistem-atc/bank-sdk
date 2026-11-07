<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\CallbackAttemptPage;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\RetryCallbackResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Pix\WebhookCompleto;

/**
 * Webhook de Pix recebido, por chave — `/pix/v2/webhook/{chave}`. Escopos
 * `webhook.read` / `webhook.write`.
 */
final class WebhookPixMethods extends BaseMethods
{
    public function cadastrar(string $chave, string $webhookUrl): void
    {
        $this->makeRequest(HttpMethod::PUT, '/webhook/'.rawurlencode($chave), body: ['webhookUrl' => $webhookUrl]);
    }

    public function consultar(string $chave): WebhookCompleto
    {
        return WebhookCompleto::fromArray($this->makeRequest(HttpMethod::GET, '/webhook/'.rawurlencode($chave)));
    }

    public function excluir(string $chave): void
    {
        $this->makeRequest(HttpMethod::DELETE, '/webhook/'.rawurlencode($chave));
    }

    /** @param array{pagina?: int, tamanhoPagina?: int, txid?: string} $filtros */
    public function callbacks(string $dataHoraInicio, string $dataHoraFim, array $filtros = []): CallbackAttemptPage
    {
        $data = $this->makeRequest(HttpMethod::GET, '/webhook/callbacks', query: self::filtros([
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim' => $dataHoraFim,
        ] + $filtros));

        return CallbackAttemptPage::fromArray($data);
    }

    /** @param array<string, mixed> $dados corpo RetryCallbacksRequestBody */
    public function reenviar(array $dados): RetryCallbackResponse
    {
        return RetryCallbackResponse::fromArray($this->makeRequest(HttpMethod::POST, '/webhook/callbacks/retry', body: $dados));
    }
}
