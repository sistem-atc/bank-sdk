<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

use Illuminate\Http\Request;

/** Regras de recebimento de um banco: tipos aceitos, autenticação e cabeçalhos guardados. */
interface WebhookReceiver
{
    /** O tipo veio na URL e é um aviso que este banco envia? */
    public function supports(string $tipo): bool;

    /**
     * A requisição veio mesmo do banco?
     *
     * @throws WebhookReceiverNotConfigured quando falta configuração do host
     */
    public function authenticate(Request $request, string $integrationId): bool;

    /**
     * Tipo do aviso entregue ao host. Em geral o da URL; banco que manda todos
     * os avisos na mesma URL (Asaas) devolve o tipo que vem no payload.
     *
     * @param  array<mixed>  $payload
     */
    public function tipo(string $tipoDaUrl, array $payload): string;

    /**
     * Cabeçalhos que vão junto com o payload pro host.
     *
     * @return array<string, string>
     */
    public function headers(Request $request): array;
}
