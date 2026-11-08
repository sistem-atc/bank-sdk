<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

use Illuminate\Http\Request;
use SistemAtc\Banks\Contracts\AsaasIntegration;
use SistemAtc\Banks\Contracts\WebhookIntegrationResolver;

/**
 * Recebimento dos avisos do Asaas.
 *
 * O webhook é cadastrado no painel do Asaas (Integrações → Webhooks) com a URL
 * `{prefix}/asaas/{integração}/eventos` e um token de autenticação. O Asaas
 * manda todos os eventos nessa URL, com o token no cabeçalho
 * `asaas-access-token`; o SDK compara com o getWebhookToken() da integração,
 * que o host entrega pelo WebhookIntegrationResolver.
 *
 * O tipo entregue ao host é o evento do payload (PAYMENT_RECEIVED,
 * TRANSFER_DONE…). Pra trabalhar com DTO tipado:
 * `AsaasWebhookPayload::map($evento->payload)`.
 */
final class AsaasWebhookReceiver implements WebhookReceiver
{
    /** Único tipo da URL: o Asaas manda todos os eventos no mesmo endereço. */
    public const TIPO = 'eventos';

    public const TOKEN_HEADER = 'asaas-access-token';

    public function supports(string $tipo): bool
    {
        return $tipo === self::TIPO;
    }

    public function authenticate(Request $request, string $integrationId): bool
    {
        if (! app()->bound(WebhookIntegrationResolver::class)) {
            throw new WebhookReceiverNotConfigured(
                'Asaas: o host não registrou um '.WebhookIntegrationResolver::class.'.'
            );
        }

        $integration = app(WebhookIntegrationResolver::class)->resolve('asaas', $integrationId);

        if (! $integration instanceof AsaasIntegration || ! $integration->isIntegrationActive()) {
            return false;
        }

        $esperado = trim((string) $integration->getWebhookToken());
        $recebido = trim((string) $request->header(self::TOKEN_HEADER));

        return $esperado !== '' && $recebido !== '' && hash_equals($esperado, $recebido);
    }

    public function tipo(string $tipoDaUrl, array $payload): string
    {
        $evento = $payload['event'] ?? null;

        return is_string($evento) && $evento !== '' ? $evento : $tipoDaUrl;
    }

    public function headers(Request $request): array
    {
        return [];
    }
}
