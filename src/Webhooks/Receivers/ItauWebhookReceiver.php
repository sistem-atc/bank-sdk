<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks\Receivers;

use Illuminate\Http\Request;

/**
 * Recebimento do aviso de Pix recebido do Itaú (padrão Bacen do Pix).
 *
 * O webhook é cadastrado por CHAVE Pix (`PUT /webhook/{chave}`, em
 * `recebimentosPix()->webhook()->cadastrar()`) com a URL
 * `{prefix}/itau/{integração}/pix`. O Itaú acrescenta `/pix` ao chamar, que a
 * rota absorve no sufixo. O corpo é `{"pix": [{endToEndId, txid, valor,
 * horario, ...}]}` e vale para TODO Pix recebido na chave — quem escuta filtra
 * pelo txid o que é seu.
 *
 * Autenticação: mTLS, como no Inter. O Itaú apresenta certificado de cliente;
 * quem confere é o servidor web que termina o TLS (nginx:
 * `ssl_client_certificate` com a cadeia de CA do Itaú + `ssl_verify_client
 * optional`) e repassa o resultado num cabeçalho. Só aviso com ele em SUCCESS
 * passa; sem o proxy configurado, tudo é recusado (seguro por padrão).
 *
 * `banks.webhooks.itau.client_verify_header` define o cabeçalho; vazio desliga
 * a conferência. `php artisan banks:webhooks-nginx itau --ca-path=<cadeia.crt>`
 * confere o arquivo e imprime a configuração do nginx.
 */
final class ItauWebhookReceiver implements RequiresClientCertificate, WebhookReceiver
{
    /** Único tipo: o Itaú só avisa Pix recebido nesta rota. */
    public const TIPO = 'pix';

    public function supports(string $tipo): bool
    {
        return $tipo === self::TIPO;
    }

    public function authenticate(Request $request, string $integrationId): bool
    {
        $header = $this->clientVerifyHeader();

        if ($header === '') {
            return true;
        }

        return $request->header($header) === 'SUCCESS';
    }

    public function tipo(string $tipoDaUrl, array $payload): string
    {
        return $tipoDaUrl;
    }

    public function headers(Request $request): array
    {
        return [];
    }

    public function clientVerifyHeader(): string
    {
        return (string) config('banks.webhooks.itau.client_verify_header', 'X-SSL-Client-Verify');
    }

    public function clientCaFileName(): string
    {
        return 'itau-webhook-ca.crt';
    }
}
