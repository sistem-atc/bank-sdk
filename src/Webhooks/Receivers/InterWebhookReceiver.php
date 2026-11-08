<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

use Illuminate\Http\Request;
use SistemAtc\Banks\Banks\Inter\Enums\TipoWebhook;

/**
 * Recebimento dos avisos do Inter.
 *
 * Autenticação: o Inter chama a URL com certificado de cliente assinado pela
 * CA dele (o `ca.crt` do "Certificado Webhook" do Internet Banking). Quem
 * confere o certificado é o servidor web que termina o TLS (nginx:
 * `ssl_client_certificate` + `ssl_verify_client optional`) — o PHP não vê o
 * handshake. O nginx repassa o resultado num cabeçalho, e o SDK só aceita o
 * aviso com ele em SUCCESS. Sem o cabeçalho configurado no proxy, o aviso é
 * recusado (seguro por padrão).
 *
 * `banks.webhooks.inter.client_verify_header` define o cabeçalho; vazio
 * desliga a conferência (só pra ambiente onde outra camada já garante a origem).
 */
final class InterWebhookReceiver implements WebhookReceiver
{
    public function supports(string $tipo): bool
    {
        return TipoWebhook::tryFrom($tipo) !== null;
    }

    public function authenticate(Request $request): bool
    {
        $header = (string) config('banks.webhooks.inter.client_verify_header', 'X-SSL-Client-Verify');

        if ($header === '') {
            return true;
        }

        return $request->header($header) === 'SUCCESS';
    }

    public function headers(Request $request): array
    {
        // O Inter manda a conta do aviso desde 10/04/2023 — é o que separa
        // contas de uma aplicação ligada a várias.
        $conta = $request->header('x-conta-corrente');

        return $conta === null ? [] : ['x-conta-corrente' => (string) $conta];
    }
}
