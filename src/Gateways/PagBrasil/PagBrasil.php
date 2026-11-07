<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil;

use SistemAtc\Banks\Contracts\GatewayConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Gateways\PagBrasil\Endpoints\Checkout\CheckoutMethods;
use SistemAtc\Banks\Gateways\PagBrasil\Endpoints\Orders\OrderMethods;
use SistemAtc\Banks\Gateways\PagBrasil\Endpoints\PagStream\PagStream;
use SistemAtc\Banks\Gateways\PagBrasil\Endpoints\Payout\PayoutMethods;
use SistemAtc\Banks\Gateways\PagBrasil\Endpoints\PixAutomatico\PixAutomaticoMethods;
use SistemAtc\Banks\Gateways\PagBrasil\Support\Credentials;
use SistemAtc\Banks\Gateways\PagBrasil\Support\HttpClientFactory;
use SistemAtc\Banks\Gateways\PagBrasil\Support\OAuth;
use SistemAtc\Banks\Gateways\PagBrasil\Webhooks\WebhookVerifier;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector PagBrasil — gateway de pagamento (cartão, Débito Flash, boleto,
 * Pix, Pix Automático, Link de Pagamento, Payout e assinaturas PagStream).
 * Produtos direto aqui: `PagBrasil::make()->pedidos($i)`.
 *
 * Não é banco: não tem extrato, DDA nem pagamento de saída no molde SISPAG,
 * por isso implementa GatewayConnector e não BankConnector. A liquidação
 * (repasse + taxas por transação) sai só no relatório "Settlement Reports"
 * do Dashboard: a PagBrasil não expõe API pra ele.
 *
 * A integração precisa implementar PagBrasilIntegration (pbtoken, secret
 * phrase e signature key).
 */
final class PagBrasil implements GatewayConnector
{
    public static function make(): self
    {
        return new self();
    }

    /**
     * Token OAuth de curta duração pro PagBrasil.JS (as APIs do SDK não usam
     * token — autenticam com pbtoken + secret).
     */
    public function auth(BankIntegration $integration): AuthToken
    {
        return OAuth::authenticate($integration);
    }

    /** Pedidos: cartão, Débito Flash, boleto e Pix (/api/order/*, /api/pix/1click). */
    public function pedidos(BankIntegration $integration): OrderMethods
    {
        $credentials = Credentials::of($integration);

        return new OrderMethods(HttpClientFactory::form($credentials), $credentials);
    }

    /** Link de Pagamento — checkout hospedado (/api/checkout/add). */
    public function linkPagamento(BankIntegration $integration): CheckoutMethods
    {
        $credentials = Credentials::of($integration);

        return new CheckoutMethods(HttpClientFactory::form($credentials), $credentials);
    }

    /** Pix Automático — consentimento, cobrança recorrente e retentativa. */
    public function pixAutomatico(BankIntegration $integration): PixAutomaticoMethods
    {
        $credentials = Credentials::of($integration);

        return new PixAutomaticoMethods(HttpClientFactory::form($credentials), $credentials);
    }

    /** Payout — favorecidos e envio de valores (/api/payout/). */
    public function payout(BankIntegration $integration): PayoutMethods
    {
        $credentials = Credentials::of($integration);

        return new PayoutMethods(HttpClientFactory::form($credentials), $credentials);
    }

    /** PagStream — assinaturas, recorrências, envios, catálogo e cobranças. */
    public function pagStream(BankIntegration $integration): PagStream
    {
        return new PagStream(Credentials::of($integration));
    }

    /** Autenticação e leitura de IPNs/webhooks recebidos da PagBrasil. */
    public function webhook(BankIntegration $integration): WebhookVerifier
    {
        return new WebhookVerifier(Credentials::of($integration));
    }
}
