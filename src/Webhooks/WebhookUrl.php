<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Webhooks;

use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * URL pública que o SDK registra no banco pra receber os avisos:
 *
 *   {base}/{prefix}/{banco}/{integração}/{tipo}
 *   https://erp.exemplo.com.br/api/webhooks/banks/inter/42/pix
 *
 * `base` é `banks.webhooks.base_url` ou, sem ele, `app.url`. A integração vai
 * na URL porque o mesmo app pode atender várias empresas (cada uma com a sua
 * credencial) e o aviso nem sempre diz de quem é.
 */
final class WebhookUrl
{
    public static function for(string $bank, BankIntegration|int|string $integration, string $tipo): string
    {
        $id = $integration instanceof BankIntegration ? $integration->getIntegrationIdentifier() : $integration;

        $base = rtrim((string) (config('banks.webhooks.base_url') ?: config('app.url')), '/');
        $prefix = trim((string) config('banks.webhooks.prefix', 'api/webhooks/banks'), '/');

        return $base.'/'.$prefix.'/'.rawurlencode($bank).'/'.rawurlencode((string) $id).'/'.rawurlencode($tipo);
    }
}
