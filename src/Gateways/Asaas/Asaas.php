<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\Asaas;

use SistemAtc\Banks\Contracts\AsaasIntegration;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Contracts\GatewayConnector;
use SistemAtc\Banks\Gateways\Asaas\Support\HttpClientFactory;
use SistemAtc\Banks\Gateways\Asaas\Methods\AccountDocument;
use SistemAtc\Banks\Gateways\Asaas\Methods\AccountInfo;
use SistemAtc\Banks\Gateways\Asaas\Methods\Anticipation;
use SistemAtc\Banks\Gateways\Asaas\Methods\AutomaticPix;
use SistemAtc\Banks\Gateways\Asaas\Methods\Bill;
use SistemAtc\Banks\Gateways\Asaas\Methods\Chargeback;
use SistemAtc\Banks\Gateways\Asaas\Methods\Checkout;
use SistemAtc\Banks\Gateways\Asaas\Methods\CreditBureauReport;
use SistemAtc\Banks\Gateways\Asaas\Methods\CreditCard;
use SistemAtc\Banks\Gateways\Asaas\Methods\Customer;
use SistemAtc\Banks\Gateways\Asaas\Methods\EscrowAccount;
use SistemAtc\Banks\Gateways\Asaas\Methods\Finance;
use SistemAtc\Banks\Gateways\Asaas\Methods\FinancialTransaction;
use SistemAtc\Banks\Gateways\Asaas\Methods\FiscalInfo;
use SistemAtc\Banks\Gateways\Asaas\Methods\Installment;
use SistemAtc\Banks\Gateways\Asaas\Methods\Invoice;
use SistemAtc\Banks\Gateways\Asaas\Methods\MobilePhoneRecharge;
use SistemAtc\Banks\Gateways\Asaas\Methods\Notification;
use SistemAtc\Banks\Gateways\Asaas\Methods\Payment;
use SistemAtc\Banks\Gateways\Asaas\Methods\PaymentDocument;
use SistemAtc\Banks\Gateways\Asaas\Methods\PaymentDunning;
use SistemAtc\Banks\Gateways\Asaas\Methods\PaymentLink;
use SistemAtc\Banks\Gateways\Asaas\Methods\PaymentRefund;
use SistemAtc\Banks\Gateways\Asaas\Methods\PaymentSplit;
use SistemAtc\Banks\Gateways\Asaas\Methods\PaymentWithSummaryData;
use SistemAtc\Banks\Gateways\Asaas\Methods\Pix;
use SistemAtc\Banks\Gateways\Asaas\Methods\PixTransaction;
use SistemAtc\Banks\Gateways\Asaas\Methods\RecurringPix;
use SistemAtc\Banks\Gateways\Asaas\Methods\Subaccount;
use SistemAtc\Banks\Gateways\Asaas\Methods\Subscription;
use SistemAtc\Banks\Gateways\Asaas\Methods\Transfer;
use SistemAtc\Banks\Gateways\Asaas\Methods\Webhook;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector do Asaas — gateway de cobrança (boleto, Pix, cartão, assinaturas,
 * split, subcontas, transferências, NFS-e). Veio do pacote
 * `sistem_atc/asaas-laravel` (2.0.2): Methods e DTOs são os mesmos, com a
 * credencial por integração em vez de config global.
 *
 *   Asaas::make()->payment($i)->createNewPayment(CreatePaymentRequestDTO::fromArray([...]));
 *   Asaas::make()->financialTransaction($i)->...;   // extrato
 *
 * A integração implementa AsaasIntegration (chave de API + token do webhook);
 * o ambiente vem de isSandbox(). Avisos chegam pela rota de webhook do SDK
 * (ver Webhooks\Receivers\AsaasWebhookReceiver).
 */
final class Asaas implements GatewayConnector
{
    public static function make(): self
    {
        return new self();
    }

    /**
     * O Asaas não tem OAuth: a chave de API da conta é a credencial, sem
     * expiração. Devolve a chave como token pra cumprir o contrato.
     */
    public function auth(BankIntegration $integration): AuthToken
    {
        HttpClientFactory::for($integration);

        /** @var AsaasIntegration $integration */
        return new AuthToken(
            accessToken: $integration->getApiKey(),
            expiresIn: 0,
            tokenType: 'access_token',
            obtainedAt: time(),
        );
    }

    /** Documentos da conta (envio e situação). */
    public function accountDocument(BankIntegration $integration): AccountDocument
    {
        return new AccountDocument(HttpClientFactory::for($integration));
    }

    /** Dados comerciais, personalização do checkout, white label. */
    public function accountInfo(BankIntegration $integration): AccountInfo
    {
        return new AccountInfo(HttpClientFactory::for($integration));
    }

    /** Antecipação de recebíveis. */
    public function anticipation(BankIntegration $integration): Anticipation
    {
        return new Anticipation(HttpClientFactory::for($integration));
    }

    /** Pagamento de contas (boletos de terceiros). */
    public function bill(BankIntegration $integration): Bill
    {
        return new Bill(HttpClientFactory::for($integration));
    }

    /** Chargebacks de cartão. */
    public function chargeback(BankIntegration $integration): Chargeback
    {
        return new Chargeback(HttpClientFactory::for($integration));
    }

    /** Checkout hospedado. */
    public function checkout(BankIntegration $integration): Checkout
    {
        return new Checkout(HttpClientFactory::for($integration));
    }

    /** Consulta Serasa. */
    public function creditBureauReport(BankIntegration $integration): CreditBureauReport
    {
        return new CreditBureauReport(HttpClientFactory::for($integration));
    }

    /** Tokenização de cartão. */
    public function creditCard(BankIntegration $integration): CreditCard
    {
        return new CreditCard(HttpClientFactory::for($integration));
    }

    /** Clientes (pagadores). */
    public function customer(BankIntegration $integration): Customer
    {
        return new Customer(HttpClientFactory::for($integration));
    }

    /** Conta escrow (garantia). */
    public function escrowAccount(BankIntegration $integration): EscrowAccount
    {
        return new EscrowAccount(HttpClientFactory::for($integration));
    }

    /** Saldo, estatísticas e extrato de cobranças. */
    public function finance(BankIntegration $integration): Finance
    {
        return new Finance(HttpClientFactory::for($integration));
    }

    /** Extrato (movimentações financeiras). */
    public function financialTransaction(BankIntegration $integration): FinancialTransaction
    {
        return new FinancialTransaction(HttpClientFactory::for($integration));
    }

    /** Dados fiscais para emissão de NFS-e. */
    public function fiscalInfo(BankIntegration $integration): FiscalInfo
    {
        return new FiscalInfo(HttpClientFactory::for($integration));
    }

    /** Notas fiscais de serviço. */
    public function invoice(BankIntegration $integration): Invoice
    {
        return new Invoice(HttpClientFactory::for($integration));
    }

    /** Recarga de celular. */
    public function mobilePhoneRecharge(BankIntegration $integration): MobilePhoneRecharge
    {
        return new MobilePhoneRecharge(HttpClientFactory::for($integration));
    }

    /** Notificações enviadas aos clientes. */
    public function notification(BankIntegration $integration): Notification
    {
        return new Notification(HttpClientFactory::for($integration));
    }

    /** Cobranças (boleto, Pix, cartão). */
    public function payment(BankIntegration $integration): Payment
    {
        return new Payment(HttpClientFactory::for($integration));
    }

    /** Documentos anexados às cobranças. */
    public function paymentDocument(BankIntegration $integration): PaymentDocument
    {
        return new PaymentDocument(HttpClientFactory::for($integration));
    }

    /** Negativação. */
    public function paymentDunning(BankIntegration $integration): PaymentDunning
    {
        return new PaymentDunning(HttpClientFactory::for($integration));
    }

    /** Links de pagamento. */
    public function paymentLink(BankIntegration $integration): PaymentLink
    {
        return new PaymentLink(HttpClientFactory::for($integration));
    }

    /** Cobranças com dados resumidos. */
    public function paymentWithSummaryData(BankIntegration $integration): PaymentWithSummaryData
    {
        return new PaymentWithSummaryData(HttpClientFactory::for($integration));
    }

    /** Estornos. */
    public function paymentRefund(BankIntegration $integration): PaymentRefund
    {
        return new PaymentRefund(HttpClientFactory::for($integration));
    }

    /** Parcelamentos. */
    public function installment(BankIntegration $integration): Installment
    {
        return new Installment(HttpClientFactory::for($integration));
    }

    /** Split de pagamentos. */
    public function paymentSplit(BankIntegration $integration): PaymentSplit
    {
        return new PaymentSplit(HttpClientFactory::for($integration));
    }

    /** Chaves Pix e QR Code estático. */
    public function pix(BankIntegration $integration): Pix
    {
        return new Pix(HttpClientFactory::for($integration));
    }

    /** Transações Pix (pagar QR Code, listar, cancelar). */
    public function pixTransaction(BankIntegration $integration): PixTransaction
    {
        return new PixTransaction(HttpClientFactory::for($integration));
    }

    /** Pix Automático. */
    public function automaticPix(BankIntegration $integration): AutomaticPix
    {
        return new AutomaticPix(HttpClientFactory::for($integration));
    }

    /** Pix recorrente. */
    public function recurringPix(BankIntegration $integration): RecurringPix
    {
        return new RecurringPix(HttpClientFactory::for($integration));
    }

    /** Subcontas. */
    public function subAccount(BankIntegration $integration): Subaccount
    {
        return new Subaccount(HttpClientFactory::for($integration));
    }

    /** Assinaturas. */
    public function subscription(BankIntegration $integration): Subscription
    {
        return new Subscription(HttpClientFactory::for($integration));
    }

    /** Transferências (TED/Pix) para outras contas. */
    public function transfer(BankIntegration $integration): Transfer
    {
        return new Transfer(HttpClientFactory::for($integration));
    }

    /** Cadastro de webhooks pela API. */
    public function webhook(BankIntegration $integration): Webhook
    {
        return new Webhook(HttpClientFactory::for($integration));
    }
}
