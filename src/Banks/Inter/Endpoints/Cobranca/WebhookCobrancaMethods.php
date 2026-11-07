<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Cobranca;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\CallbackAttemptPage;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\RetryCallbackResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca\WebhookCompleto;

/**
 * Webhook da Cobrança v3 (`/cobranca/v3/cobrancas/webhook`): avisos de
 * cobrança paga, cancelada, expirada… Um webhook por conta.
 */
final class WebhookCobrancaMethods extends BaseMethods
{
    public function cadastrar(string $webhookUrl): void
    {
        $this->makeRequest(HttpMethod::PUT, '/cobrancas/webhook', body: ['webhookUrl' => $webhookUrl]);
    }

    public function consultar(): WebhookCompleto
    {
        return WebhookCompleto::fromArray($this->makeRequest(HttpMethod::GET, '/cobrancas/webhook'));
    }

    public function excluir(): void
    {
        $this->makeRequest(HttpMethod::DELETE, '/cobrancas/webhook');
    }

    /** @param  array{pagina?: int, tamanhoPagina?: int, codigoSolicitacao?: string}  $filtros */
    public function callbacks(string $dataHoraInicio, string $dataHoraFim, array $filtros = []): CallbackAttemptPage
    {
        $data = $this->makeRequest(HttpMethod::GET, '/cobrancas/webhook/callbacks', query: self::filtros([
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim' => $dataHoraFim,
        ] + $filtros));

        return CallbackAttemptPage::fromArray($data);
    }

    /**
     * Reenvia callbacks que falharam.
     *
     * ⚠️ Path como está na spec oficial (`/webhook/callbacks/retry`, sem o
     * `/cobrancas` que os outros endpoints de webhook têm). Se o Inter
     * responder 404 em produção, o path certo é o com `/cobrancas`.
     *
     * @param  array<string, mixed>  $dados  corpo RetryCallbacksRequestBody
     */
    public function reenviar(array $dados): RetryCallbackResponse
    {
        return RetryCallbackResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, '/webhook/callbacks/retry', body: $dados),
        );
    }
}
