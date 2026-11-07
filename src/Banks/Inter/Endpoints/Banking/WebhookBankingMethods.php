<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Banking;

use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Banks\Inter\Bases\BaseMethods;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\CallbackAttemptPage;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\RetryCallbackResponse;
use SistemAtc\Banks\Banks\Inter\DTO\Response\Banking\WebhookModel;
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhookBanking;

/**
 * Webhooks da API Banking (`/banking/v2/webhooks/{tipoWebhook}`): avisos de
 * Pix pago e boleto pago. Escopos `webhook-banking.read` / `.write`.
 */
final class WebhookBankingMethods extends BaseMethods
{
    /** `PUT` — cadastra/troca a URL (HTTPS) do webhook. */
    public function cadastrar(TipoWebhookBanking $tipo, string $webhookUrl): void
    {
        $this->makeRequest(HttpMethod::PUT, $this->path($tipo), body: ['webhookUrl' => $webhookUrl]);
    }

    public function consultar(TipoWebhookBanking $tipo): WebhookModel
    {
        return WebhookModel::fromArray($this->makeRequest(HttpMethod::GET, $this->path($tipo)));
    }

    public function excluir(TipoWebhookBanking $tipo): void
    {
        $this->makeRequest(HttpMethod::DELETE, $this->path($tipo));
    }

    /**
     * `GET .../callbacks` — tentativas de entrega no período (obrigatório),
     * em ISO 8601 com fuso: `2026-11-01T00:00Z` ou `2026-11-01T00:00:00.000-03:00`.
     *
     * @param  array{pagina?: int, tamanhoPagina?: int, endToEnd?: string, codigoTransacao?: string}  $filtros
     */
    public function callbacks(TipoWebhookBanking $tipo, string $dataHoraInicio, string $dataHoraFim, array $filtros = []): CallbackAttemptPage
    {
        $data = $this->makeRequest(HttpMethod::GET, $this->path($tipo).'/callbacks', query: self::filtros([
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim' => $dataHoraFim,
        ] + $filtros));

        return CallbackAttemptPage::fromArray($data);
    }

    /**
     * `POST .../callbacks/retry` — reenvia callbacks que falharam.
     *
     * @param  array<string, mixed>  $dados  corpo RetryCallbacksRequestBody do Inter
     */
    public function reenviar(TipoWebhookBanking $tipo, array $dados): RetryCallbackResponse
    {
        return RetryCallbackResponse::fromArray(
            $this->makeRequest(HttpMethod::POST, $this->path($tipo).'/callbacks/retry', body: $dados),
        );
    }

    private function path(TipoWebhookBanking $tipo): string
    {
        return '/webhooks/'.$tipo->value;
    }
}
