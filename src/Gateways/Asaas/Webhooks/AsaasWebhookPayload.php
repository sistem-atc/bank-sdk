<?php

namespace SistemAtc\Banks\Gateways\Asaas\Webhooks;

use Illuminate\Support\Str;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\BillWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\MobileWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\BalanceWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\InvoiceWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\PaymentWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\CheckoutWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\InternalWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\PixWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\TransferWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\ReceivableWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\AccessTokenWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\Contracts\WebhookEventDTOInterface;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\SubscriptionWebhookDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Webhook\AccountStatusWebhookDTO;

/**
 * Converte o payload de um aviso do Asaas no DTO tipado do evento (pelo
 * prefixo: PAYMENT_*, TRANSFER_*, PIX_*…). O recebimento é da rota do SDK
 * (AsaasWebhookReceiver → BankWebhookReceived); isto serve pro host processar
 * o payload que gravou.
 *
 * @throws \InvalidArgumentException evento desconhecido
 */
final class AsaasWebhookPayload
{
    public static function map(array $payload): WebhookEventDTOInterface
    {
        $event = $payload['event'] ?? '';
        $prefix = Str::before($event, '_');

        return match (strtoupper($prefix)) {
            'PAYMENT'       => PaymentWebhookDTO::fromArray($payload),
            'SUBSCRIPTION'  => SubscriptionWebhookDTO::fromArray($payload),
            'INVOICE'       => InvoiceWebhookDTO::fromArray($payload),
            'TRANSFER'      => TransferWebhookDTO::fromArray($payload),
            'BILL'          => BillWebhookDTO::fromArray($payload),
            'RECEIVABLE'    => ReceivableWebhookDTO::fromArray($payload),
            'MOBILE'        => MobileWebhookDTO::fromArray($payload),
            'ACCOUNT'       => AccountStatusWebhookDTO::fromArray($payload),
            'CHECKOUT'      => CheckoutWebhookDTO::fromArray($payload),
            'BALANCE'       => BalanceWebhookDTO::fromArray($payload),
            'INTERNAL'      => InternalWebhookDTO::fromArray($payload),
            'ACCESS'        => AccessTokenWebhookDTO::fromArray($payload),
            'PIX'           => PixWebhookDTO::fromArray($payload),
            default         => self::handleUnknownEvent($event),
        };
    }

    private static function handleUnknownEvent(string $event): never
    {
        logger()->error("Asaas Webhook: Evento desconhecido recebido: {$event}");
        throw new \InvalidArgumentException("Unsupported Asaas event: {$event}");
    }

}
