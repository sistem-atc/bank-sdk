<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Bases;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\Exceptions\PagBrasilRequestException;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;

/**
 * Base da API REST do PagStream (/api/v2/pagstream): JSON, credenciais em
 * headers, erro com status HTTP e o envelope
 *
 *   {"error": {"type": "...", "code": "subscription_not_paused", "message": "...", "details": {}}}
 *
 * que vira PagBrasilRequestException com errorCode/errorType/details. A doc
 * manda ramificar pelo `code`, nunca pela mensagem.
 *
 * Só leituras (GET) repetem sozinhas em 429/5xx/queda — 5xx é "condição
 * temporária, pode repetir", mas uma escrita repetida às cegas pode, por
 * exemplo, pular uma recorrência a mais.
 */
abstract class RestMethods
{
    protected const BASE = '/api/v2/pagstream';

    private const MAX_RETRIES = 2;

    public function __construct(
        protected PendingRequest $httpClient,
        protected PagBrasilIntegration $integration,
    ) {}

    /**
     * DTO de request ou array cru (nomes da doc) → campos da API.
     *
     * @param  RequestPayload|array<int|string, mixed>  $data
     * @return array<int|string, mixed>
     */
    protected function payload(RequestPayload|array $data): array
    {
        return $data instanceof RequestPayload ? $data->toArray() : $data;
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<int|string, mixed>|null  $body  null = sem corpo; lista vira array JSON
     * @return array<int|string, mixed>
     */
    protected function request(HttpMethod $method, string $path, array $query = [], ?array $body = null): array
    {
        $url = self::BASE.$path;
        $query = array_filter($query, fn ($v) => $v !== null && $v !== '');
        $idempotent = $method === HttpMethod::GET;
        $attempt = 0;

        while (true) {
            try {
                $response = $this->send($method, $url, $query, $body);
            } catch (ConnectionException $e) {
                if ($idempotent && $attempt < self::MAX_RETRIES) {
                    sleep(2 ** ++$attempt);

                    continue;
                }

                throw $e;
            }

            if ($idempotent && $attempt < self::MAX_RETRIES
                && ($response->status() === 429 || $response->serverError())) {
                $attempt++;
                $retryAfter = $response->header('Retry-After');
                sleep($retryAfter !== '' ? (int) $retryAfter : 2 ** $attempt);

                continue;
            }

            break;
        }

        if ($response->failed()) {
            $this->fail($response);
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    /**
     * Número da recorrência no path: inteiro ≥ 0 ou o alias `next` (a
     * recorrência pendente, não pulada nem pausada, de vencimento mais próximo).
     */
    protected function recurrence(int|string $recurrence): string
    {
        return is_int($recurrence) ? (string) $recurrence : rawurlencode($recurrence);
    }

    protected function segment(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<int|string, mixed>|null  $body
     */
    private function send(HttpMethod $method, string $url, array $query, ?array $body): Response
    {
        $withQuery = $url.($query !== [] ? '?'.http_build_query($query) : '');

        return match ($method) {
            HttpMethod::GET => $this->httpClient->get($url, $query),
            HttpMethod::POST => $this->httpClient->post($withQuery, $body ?? []),
            HttpMethod::PUT => $this->httpClient->put($withQuery, $body ?? []),
            HttpMethod::PATCH => $this->httpClient->patch($withQuery, $body ?? []),
            HttpMethod::DELETE => $this->httpClient->delete($withQuery, $body ?? []),
        };
    }

    private function fail(Response $response): never
    {
        $error = $response->json('error');
        $error = is_array($error) ? $error : [];

        $code = isset($error['code']) ? (string) $error['code'] : null;
        $message = isset($error['message']) && $error['message'] !== ''
            ? (string) $error['message']
            : 'HTTP '.$response->status();

        Log::warning('PagBrasil PagStream API error', [
            'status' => $response->status(),
            'code' => $code,
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);

        throw new PagBrasilRequestException(
            $response,
            $code !== null ? "{$code}: {$message}" : $message,
            errorCode: $code,
            errorType: isset($error['type']) ? (string) $error['type'] : null,
            details: is_array($error['details'] ?? null) ? $error['details'] : [],
        );
    }
}
