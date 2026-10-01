<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Bases;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\Exceptions\PagBrasilRequestException;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\Exceptions\PagBrasilSignatureException;
use SistemAtc\Banks\PagBrasil\Support\Credentials;
use SistemAtc\Banks\PagBrasil\Support\ParsedResponse;
use SistemAtc\Banks\PagBrasil\Support\Signature;

/**
 * Base da API CLÁSSICA da PagBrasil (/api/order/*, /api/checkout/add,
 * /api/pix/*, /api/payout/, /mock/*): POST x-www-form-urlencoded com
 * `secret` + `pbtoken` no corpo, resposta em XML, JSON ou texto.
 *
 * ⚠️ Sem retry automático em operação que cria/movimenta. A doc é explícita:
 * sem resposta do /api/order/add, NÃO reenvie antes de consultar o pedido
 * (/api/order/get) — reenviar às cegas cobra o cliente duas vezes. Por isso
 * só as leituras (`idempotent: true`) repetem em 429/5xx/queda de conexão.
 */
abstract class FormMethods
{
    private const MAX_RETRIES = 2;

    /** Response HTTP da última chamada (pra montar a exceção fora do post()). */
    private ?Response $lastResponse = null;

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
     * @param  array<string, mixed>  $params
     */
    protected function post(string $path, array $params = [], bool $idempotent = false): ParsedResponse
    {
        $body = $this->formBody($params);
        $attempt = 0;

        while (true) {
            try {
                $response = $this->httpClient->post($path, $body);
            } catch (ConnectionException $e) {
                if ($idempotent && $attempt < self::MAX_RETRIES) {
                    $this->backoff($attempt++);

                    continue;
                }

                throw $e;
            }

            if ($idempotent && $attempt < self::MAX_RETRIES
                && ($response->status() === 429 || $response->serverError())) {
                $this->backoff($attempt++, $response);

                continue;
            }

            break;
        }

        $this->lastResponse = $response;
        $parsed = ParsedResponse::parse($response->body());

        // A PRODUÇÃO usa 4xx com corpo de verdade: pedido inexistente volta
        // 412 com `<request></request>` — o mesmo corpo que a doc documenta
        // com 200 (visto em connect.pagbrasil.com, 01/10/2026). Corpo
        // estruturado (XML/JSON) vai pro endpoint decidir; só texto e 5xx são
        // erro aqui ("Invalid access." = credencial recusada).
        if ($response->clientError() && ! $parsed->isText()) {
            return $parsed;
        }

        if ($response->failed()) {
            $this->fail($response, $parsed->isText() && $parsed->text() !== ''
                ? $parsed->text()
                : 'HTTP '.$response->status());
        }

        return $parsed;
    }

    /**
     * Espera XML/JSON. Texto no lugar da estrutura é mensagem de erro da
     * PagBrasil (HTTP 200). Confere a assinatura quando há chave.
     *
     * @return array<string, mixed>
     */
    protected function expectStructure(ParsedResponse $parsed, bool $allowEmpty = false): array
    {
        if ($parsed->isText()) {
            $this->fail($this->lastResponse(), $parsed->text() !== '' ? $parsed->text() : 'Resposta vazia.');
        }

        if ($parsed->isEmpty() && ! $allowEmpty) {
            $this->fail($this->lastResponse(), 'Resposta sem dados (pedido inexistente?).');
        }

        $this->verifySignature($parsed->data);

        return $parsed->data;
    }

    /**
     * Espera uma confirmação em texto (ex.: "Refund request received").
     * Qualquer outro texto — ou uma estrutura — é erro.
     */
    protected function expectText(ParsedResponse $parsed, string ...$successPrefixes): string
    {
        $text = $parsed->text();

        if ($parsed->isText()) {
            foreach ($successPrefixes as $prefix) {
                if (stripos($text, $prefix) === 0) {
                    return $text;
                }
            }
        }

        $this->fail($this->lastResponse(), $text !== '' ? $text : 'Resposta vazia.');
    }

    /**
     * Confere o `signature` de uma resposta estruturada. Sem chave cadastrada,
     * sem campo `signature` na resposta ou com a conferência desligada no
     * config, não há o que conferir.
     *
     * @param  array<string, mixed>  $data
     */
    protected function verifySignature(array $data): void
    {
        $key = Credentials::signatureKey($this->integration);

        if ($key === null || ! isset($data['signature']) || ! is_string($data['signature'])
            || ! config('banks.pagbrasil.verify_response_signature', true)) {
            return;
        }

        if (! Signature::matches($data['signature'], Signature::valuesOf($data), $key)) {
            throw new PagBrasilSignatureException(
                'PagBrasil: a assinatura da resposta não confere com a signature key da integração.'
            );
        }
    }

    protected function fail(Response $response, string $detail, ?string $code = null): never
    {
        Log::warning('PagBrasil API error', [
            'status' => $response->status(),
            'detail' => $detail,
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);

        throw new PagBrasilRequestException($response, $detail, errorCode: $code);
    }

    protected function lastResponse(): Response
    {
        return $this->lastResponse ?? throw new \LogicException('Nenhuma chamada feita ainda.');
    }

    /**
     * Credenciais primeiro, depois os parâmetros. Normaliza os tipos pro
     * formato que a PagBrasil espera: float com 2 casas ("39.50"), bool como
     * "1"/"0", array (ex.: `products`) como JSON. null é omitido.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, string>
     */
    private function formBody(array $params): array
    {
        $body = [
            'secret' => $this->integration->getSecretPhrase(),
            'pbtoken' => $this->integration->getPbToken(),
        ];

        foreach ($params as $name => $value) {
            if ($value === null || $name === 'secret' || $name === 'pbtoken') {
                continue;
            }

            $body[$name] = match (true) {
                is_bool($value) => $value ? '1' : '0',
                is_float($value) => number_format($value, 2, '.', ''),
                is_array($value) => (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $value instanceof \BackedEnum => (string) $value->value,
                $value instanceof \DateTimeInterface => $value->format('Y-m-d'),
                default => (string) $value,
            };
        }

        return $body;
    }

    private function backoff(int $attempt, ?Response $response = null): void
    {
        $retryAfter = $response?->header('Retry-After');

        sleep($retryAfter !== null && $retryAfter !== '' ? (int) $retryAfter : 2 ** ($attempt + 1));
    }
}
