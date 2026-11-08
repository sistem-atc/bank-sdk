<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use SistemAtc\Banks\Webhooks\BankWebhookReceived;
use SistemAtc\Banks\Webhooks\Receivers\WebhookReceivers;

/**
 * Recebe os avisos dos bancos na rota que o SDK registra
 * (`POST {prefix}/{banco}/{integração}/{tipo}`) e entrega ao host pelo evento
 * BankWebhookReceived.
 *
 * Respostas: 200 gravado · 401 origem não confirmada · 404 banco/tipo
 * desconhecido · 422 corpo que não é JSON · 503 host sem listener · 500 se o
 * listener falhar (o banco reenvia).
 */
final class BankWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Pelo NOME: `bank` vem do defaults() da rota e não tem posição fixa
        // entre os parâmetros da URL.
        $route = $request->route();
        $bank = (string) $route->parameter('bank');
        $integration = (string) $route->parameter('integration');
        $tipo = (string) $route->parameter('tipo');

        $receiver = WebhookReceivers::for($bank);

        if ($receiver === null || ! $receiver->supports($tipo)) {
            return response()->json(['error' => 'Webhook desconhecido'], 404);
        }

        if (! $receiver->authenticate($request)) {
            return response()->json(['error' => 'Origem não confirmada'], 401);
        }

        $raw = (string) $request->getContent();
        $payload = json_decode($raw, true);

        if (! is_array($payload)) {
            return response()->json(['error' => 'Corpo não é JSON'], 422);
        }

        if (! Event::hasListeners(BankWebhookReceived::class)) {
            return response()->json(['error' => 'Recebimento não configurado'], 503);
        }

        event(new BankWebhookReceived(
            bank: $bank,
            integrationId: $integration,
            tipo: $tipo,
            payload: $payload,
            headers: $receiver->headers($request),
            rawBody: $raw,
            receivedAt: new DateTimeImmutable(),
        ));

        return response()->json(['ok' => true]);
    }
}
