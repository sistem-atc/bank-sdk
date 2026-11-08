<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

use Illuminate\Http\Request;

/** Regras de recebimento de um banco: tipos aceitos, autenticação e cabeçalhos guardados. */
interface WebhookReceiver
{
    /** O tipo veio na URL e é um aviso que este banco envia? */
    public function supports(string $tipo): bool;

    /** A requisição veio mesmo do banco? */
    public function authenticate(Request $request): bool;

    /**
     * Cabeçalhos que vão junto com o payload pro host.
     *
     * @return array<string, string>
     */
    public function headers(Request $request): array;
}
