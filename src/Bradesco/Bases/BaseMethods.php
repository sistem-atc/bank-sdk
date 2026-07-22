<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Bases;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Bradesco\Support\TokenRefresher;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankRequestException;

/**
 * Base dos grupos de métodos do Bradesco. Centraliza transporte, retry e
 * tratamento de erro — os Endpoints só descrevem path + payload e mapeiam a
 * resposta pro DTO.
 *
 * ⚠️ ATENÇÃO — ERRO DE NEGÓCIO COM STATUS DE SUCESSO
 *
 * O Bradesco sinaliza falha de negócio SEM usar código HTTP de erro, então
 * `makeRequest()` NÃO lança nesses casos — a resposta chega normalmente e o
 * resultado vem dentro do DTO. Confirmado em três produtos:
 *
 *   - TED: HTTP 200 com `codigoDeErro` / `codigoDeRetorno` / `codigoDaMensagem`
 *     preenchidos. Uma TED recusada volta como 200.
 *   - Pix transferências (SPI): HTTP 202 significa transação REJEITADA
 *     (`status=REJEITADO` + `codigoMotivo`), não "aceita".
 *   - Arrecadação/Pagamentos: códigos de retorno no corpo do 200.
 *
 * Isso é DELIBERADO: em vários desses casos a recusa é um desfecho de negócio
 * legítimo que o chamador precisa inspecionar (motivo, código), não uma
 * exceção. Lançar automaticamente também exigiria adivinhar o campo de erro,
 * que muda de produto pra produto.
 *
 * Consequência prática: NUNCA trate "sem exceção" como "deu certo" num fluxo
 * que movimenta dinheiro. Cheque o campo de status/código do DTO antes de dar
 * a operação por concluída — e, em timeout, CONSULTE antes de reenviar (a
 * maioria dos endpoints não tem chave de idempotência dedicada).
 */
abstract class BaseMethods
{
    protected PendingRequest $httpClient;

    public function __construct(
        PendingRequest $httpClient,
        protected BankIntegration $integration,
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Família de autorizador/host deste grupo de métodos. Produtos Pix
     * sobrescrevem para FAMILY_PIX — o Bradesco tem dois autorizadores, e o
     * token de um não vale no outro.
     */
    protected function family(): string
    {
        return BradescoHosts::FAMILY_OPEN_API;
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $apiPath,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0,
    ): array {
        $response = $this->executeRequest($method, $apiPath, $query, $body);

        // Throttle / indisponibilidade transitória: backoff exponencial.
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);

            return $this->makeRequest($method, $apiPath, $query, $body, $retryAttempt + 1);
        }

        // 401/403: token pode ter expirado no servidor antes do nosso expires_in.
        // Descarta o token EM CACHE da família e reconstrói o client (força
        // reautenticação no autorizador certo) antes de tentar 1x.
        if (in_array($response->status(), [401, 403], true) && $retryAttempt === 0) {
            TokenRefresher::forget($this->integration, $this->family());
            $this->httpClient = HttpClientFactory::make($this->integration, $this->family());

            return $this->makeRequest($method, $apiPath, $query, $body, $retryAttempt + 1);
        }

        $data = $response->json() ?? [];

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     */
    protected function executeRequest(HttpMethod $method, string $apiPath, array $query, array $body): Response
    {
        $client = $this->httpClient;

        return match ($method) {
            HttpMethod::GET => $client->get($apiPath, $query),
            HttpMethod::POST => $client->post($apiPath.($query ? '?'.http_build_query($query) : ''), $body),
            HttpMethod::PUT => $client->put($apiPath.($query ? '?'.http_build_query($query) : ''), $body),
            HttpMethod::PATCH => $client->patch($apiPath.($query ? '?'.http_build_query($query) : ''), $body),
            HttpMethod::DELETE => $client->delete($apiPath.($query ? '?'.http_build_query($query) : ''), $body),
        };
    }

    protected function handleError(Response $response): void
    {
        $e = new BankRequestException($response, bank: 'bradesco');

        Log::warning('Bradesco HTTP Request Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);

        throw $e;
    }
}
