<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks;

use DateTimeImmutable;

/**
 * Um aviso (callback) recebido de um banco, já autenticado pelo recebedor do
 * banco. O host escuta este evento e GRAVA o payload antes de qualquer outra
 * coisa: o listener roda dentro da requisição, e se ele lançar exceção o SDK
 * responde 500 e o banco reenvia (o Inter tenta até 4 vezes).
 *
 * Sem nenhum listener registrado, o SDK responde 503 em vez de 200 — aviso
 * não pode ser engolido em silêncio.
 */
final class BankWebhookReceived
{
    /**
     * @param  string  $bank  'inter', ...
     * @param  string  $integrationId  identificador da integração que veio na URL
     * @param  string  $tipo  tipo do aviso (ex.: TipoWebhook do Inter)
     * @param  array<int|string, mixed>  $payload  corpo JSON como veio
     * @param  array<string, string>  $headers  cabeçalhos relevantes (ex.: x-conta-corrente)
     */
    public function __construct(
        public readonly string $bank,
        public readonly string $integrationId,
        public readonly string $tipo,
        public readonly array $payload,
        public readonly array $headers,
        public readonly string $rawBody,
        public readonly DateTimeImmutable $receivedAt,
    ) {}
}
