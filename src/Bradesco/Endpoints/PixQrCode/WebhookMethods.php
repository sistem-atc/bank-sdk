<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use SistemAtc\Banks\Bradesco\Bases\BaseMethods;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\ListaWebhooks;
use SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode\Webhook;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Webhook de notificações Bacen — o Bradesco chama a URL configurada quando um
 * Pix é liquidado. O registro é POR CHAVE Pix recebedora.
 *
 * Cobre `/v2/webhook` (listar) e `/v2/webhook/{chave}` (configurar, consultar,
 * excluir).
 *
 * FAMÍLIA PIX — host `qrpix.bradesco.com.br` e autorizador `/v2/oauth/token`.
 */
final class WebhookMethods extends BaseMethods
{
    private const PATH = '/v2/webhook';

    protected function family(): string
    {
        return BradescoHosts::FAMILY_PIX;
    }

    /**
     * Configura (ou substitui) o webhook da chave — PUT /v2/webhook/{chave}.
     *
     * @param  array{webhookUrl?: string}  $dados
     */
    public function configurar(string $chave, array $dados): DTOInterface
    {
        $data = $this->makeRequest(
            HttpMethod::PUT,
            self::PATH.'/'.rawurlencode($chave),
            body: $dados,
        );

        return Webhook::fromArray($data);
    }

    /** Consulta o webhook configurado para a chave — GET /v2/webhook/{chave}. */
    public function consultar(string $chave): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH.'/'.rawurlencode($chave));

        return Webhook::fromArray($data);
    }

    /**
     * Remove o webhook da chave — DELETE /v2/webhook/{chave}.
     * Responde 204 sem corpo; `handleError` já estoura em caso de falha.
     */
    public function excluir(string $chave): bool
    {
        $this->makeRequest(HttpMethod::DELETE, self::PATH.'/'.rawurlencode($chave));

        return true;
    }

    /**
     * Lista os webhooks configurados — GET /v2/webhook.
     *
     * @param  array<string, mixed>  $filtros  inicio, fim, 'paginacao.paginaAtual',
     *                                         'paginacao.itensPorPagina' (todos opcionais)
     */
    public function listar(array $filtros = []): DTOInterface
    {
        $data = $this->makeRequest(HttpMethod::GET, self::PATH, query: $filtros);

        // A spec do Bradesco aninha `webhooks` DENTRO de `parametros`; o padrão
        // Bacen (e o que o ambiente devolve) traz no topo. Normaliza os dois.
        if (! isset($data['webhooks']) && isset($data['parametros']['webhooks'])) {
            $data['webhooks'] = $data['parametros']['webhooks'];
        }

        return ListaWebhooks::fromArray($data);
    }
}
