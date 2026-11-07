<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Bases;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankRequestException;
use SistemAtc\Banks\Banks\Inter\Support\HttpClientFactory;

/**
 * Base dos grupos de métodos do Inter: transporte, retry e erro. Os
 * Endpoints só descrevem path + payload e hidratam o DTO.
 *
 * Cada grupo nasce com o cliente já apontado pro path base do produto
 * (`/banking/v2`, `/cobranca/v3`, `/pix/v2`). O `$basePath` fica guardado
 * porque o retry de 401/403 recria o cliente, e sem ele a 2ª tentativa
 * perderia o produto (mesma armadilha corrigida no Itaú em 01/09/2026).
 */
abstract class BaseMethods
{
    public function __construct(
        protected PendingRequest $httpClient,
        protected BankIntegration $integration,
        protected string $basePath,
    ) {}

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     * @return array<mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $apiPath,
        array $query = [],
        array $body = [],
        array $headers = [],
        int $retryAttempt = 0,
    ): array {
        $response = $this->executeRequest($method, $apiPath, $query, $body, $headers);

        // Throttle / indisponibilidade transitória: backoff exponencial.
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            sleep((int) ($response->header('Retry-After') ?: 2 ** ($retryAttempt + 1)));

            return $this->makeRequest($method, $apiPath, $query, $body, $headers, $retryAttempt + 1);
        }

        // 401/403: o token pode ter sido invalidado antes do expires_in.
        // Reconstrói o cliente (força reautenticação) no MESMO produto e tenta 1×.
        if (in_array($response->status(), [401, 403], true) && $retryAttempt === 0) {
            $this->integration->updateAccessToken('', 0);
            $this->httpClient = HttpClientFactory::make($this->integration, $this->basePath);

            return $this->makeRequest($method, $apiPath, $query, $body, $headers, $retryAttempt + 1);
        }

        if ($response->failed()) {
            $this->handleError($response);
        }

        // 202/204 (cancelar, webhook, lote) vêm sem corpo.
        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    protected function executeRequest(HttpMethod $method, string $apiPath, array $query, array $body, array $headers): Response
    {
        $client = $headers === [] ? $this->httpClient : $this->httpClient->withHeaders($headers);
        $qs = $query === [] ? '' : '?'.http_build_query($query);
        $path = ltrim($apiPath, '/');

        return match ($method) {
            HttpMethod::GET => $client->get($path, $query),
            HttpMethod::POST => $client->post($path.$qs, $body),
            HttpMethod::PUT => $client->put($path.$qs, $body),
            HttpMethod::PATCH => $client->patch($path.$qs, $body),
            HttpMethod::DELETE => $client->delete($path.$qs, $body),
        };
    }

    protected function handleError(Response $response): void
    {
        $e = new BankRequestException($response, bank: 'inter');

        Log::warning('Inter HTTP Request Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);

        throw $e;
    }

    /** Remove filtros nulos antes de montar a query string. */
    protected static function filtros(array $filtros): array
    {
        return array_filter($filtros, static fn ($v) => $v !== null && $v !== '');
    }
}
