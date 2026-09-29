<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use Illuminate\Http\Client\PendingRequest;
use SistemAtc\Banks\Common\Enums\HttpMethod;
use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\PagBrasil\Bases\RestMethods;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\Cancellation;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\NewSubscription;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\PaymentMethodChange;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\StandingItem;
use SistemAtc\Banks\PagBrasil\DTO\Request\PagStream\SubscriptionFilter;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\BillingCycleOptionList;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\LegacySubscription;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\LegacySubscriptionCreated;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\Subscription;
use SistemAtc\Banks\PagBrasil\DTO\Response\PagStream\SubscriptionList;

/**
 * Assinaturas do PagStream.
 *
 *   criar                  POST  /api/pagstream/subscription/add          (v1, form)
 *   listar                 GET   /subscriptions?tax_id|email&status&page&per_page
 *   consultar              GET   /subscriptions/{n}
 *   pausar / reativar      POST  /subscriptions/{n}/pause | /resume
 *   cancelar               POST  /subscriptions/{n}/cancel                (irreversível)
 *   opcoesCiclo            GET   /subscriptions/{n}/billing-cycle/options
 *   alterarCiclo           PATCH /subscriptions/{n}/billing-cycle
 *   alterarDiaCobranca     PATCH /subscriptions/{n}/billing-day
 *   alterarPagamento       PATCH /subscriptions/{n}/payment-method
 *   adicionar/alterar/removerItemFixo  POST /api/pagstream/subscription/item/* (v1, form)
 *
 * A criação e os itens fixos (valem pra TODAS as recorrências futuras) só
 * existem na API v1; o resto é v2. O SDK esconde a diferença.
 */
final class SubscriptionsMethods extends RestMethods
{
    public function __construct(
        PendingRequest $httpClient,
        PagBrasilIntegration $integration,
        private readonly LegacyMethods $legacy,
    ) {
        parent::__construct($httpClient, $integration);
    }

    /**
     * Cria assinatura (API v1). Obrigatórios: products, product_name,
     * customer_*, address_*, amount_brl, next_billing_date (Y-m-d),
     * billing_cycle (W, M, Q, S, Y ou código próprio). Com cc_* cobra no
     * cartão; sem cc_* cobra por Link de Pagamento; pix_rec_id liga no Pix
     * Automático. Opcionais: limit, shipping_cycle.
     *
     * @param  NewSubscription|array<string, mixed>  $dados
     */
    public function criar(NewSubscription|array $dados): LegacySubscriptionCreated
    {
        return $this->legacy->criarAssinatura($this->payload($dados));
    }

    /**
     * Assinaturas de UM cliente — `tax_id` e/ou `email` obrigatório. Filtros:
     * status (awaiting_first_payment, active, awaiting_payment, canceled,
     * expired, paused), page, per_page (máx. 100).
     *
     * @param  SubscriptionFilter|array<string, mixed>  $filtros
     */
    public function listar(SubscriptionFilter|array $filtros): SubscriptionList
    {
        return SubscriptionList::fromArray($this->request(HttpMethod::GET, '/subscriptions', query: $this->payload($filtros)));
    }

    public function consultar(string $subscriptionNumber): Subscription
    {
        return $this->subscription(HttpMethod::GET, $subscriptionNumber);
    }

    /**
     * Só assinatura `active`. Revoga o consentimento Pix Automático (não volta
     * no reativar); recorrências que vencerem pausadas são puladas.
     */
    public function pausar(string $subscriptionNumber): Subscription
    {
        return $this->subscription(HttpMethod::POST, $subscriptionNumber, '/pause');
    }

    /** Só assinatura `paused`. Não recalcula datas nem desfaz pulos. */
    public function reativar(string $subscriptionNumber): Subscription
    {
        return $this->subscription(HttpMethod::POST, $subscriptionNumber, '/resume');
    }

    /**
     * Cancela de vez. Dados opcionais: cancellation_option
     * (does_not_want_product, not_satisfied, regretted, other),
     * cancellation_reason (só com other), canceled_by (≤ 32, default "merchant").
     *
     * @param  Cancellation|array<string, mixed>  $dados
     */
    public function cancelar(string $subscriptionNumber, Cancellation|array $dados = []): Subscription
    {
        $body = $this->payload($dados);

        return $this->subscription(HttpMethod::POST, $subscriptionNumber, '/cancel', $body === [] ? null : $body);
    }

    /** Ciclos para os quais a assinatura pode migrar (conta em "SKU mode"). */
    public function opcoesCiclo(string $subscriptionNumber): BillingCycleOptionList
    {
        return BillingCycleOptionList::fromArray($this->request(
            HttpMethod::GET,
            '/subscriptions/'.$this->segment($subscriptionNumber).'/billing-cycle/options',
        ));
    }

    /**
     * Troca o ciclo pela `option_key` de opcoesCiclo(). Revoga o
     * consentimento Pix; compare limit/billing_day da resposta.
     */
    public function alterarCiclo(string $subscriptionNumber, string $optionKey): Subscription
    {
        return $this->subscription(HttpMethod::PATCH, $subscriptionNumber, '/billing-cycle', ['option_key' => $optionKey]);
    }

    /**
     * Dia fixo de cobrança (1–28) ou null pra remover. Só afeta cobranças
     * geradas daqui pra frente.
     */
    public function alterarDiaCobranca(string $subscriptionNumber, ?int $dia): Subscription
    {
        return $this->subscription(HttpMethod::PATCH, $subscriptionNumber, '/billing-day', ['billing_day' => $dia]);
    }

    /**
     * Forma de pagamento e/ou parcelas: payment_method (credit_card +
     * card_token | pix_automatico + pix_consent_token | payment_link),
     * installments.
     *
     * @param  PaymentMethodChange|array<string, mixed>  $dados
     */
    public function alterarPagamento(string $subscriptionNumber, PaymentMethodChange|array $dados): Subscription
    {
        return $this->subscription(HttpMethod::PATCH, $subscriptionNumber, '/payment-method', $this->payload($dados));
    }

    /**
     * Item fixo (v1): entra em TODAS as recorrências futuras (a pendente não
     * muda). Dados: amount_brl (total da linha), quantity, discount (VALOR em
     * R$, não percentual).
     *
     * @param  StandingItem|array<string, mixed>  $dados
     */
    public function adicionarItemFixo(string $subscription, string $sku, StandingItem|array $dados = []): LegacySubscription
    {
        return $this->legacy->item('add', $subscription, $sku, $this->payload($dados));
    }

    /** @param StandingItem|array<string, mixed> $dados */
    public function alterarItemFixo(string $subscription, string $sku, StandingItem|array $dados): LegacySubscription
    {
        return $this->legacy->item('update', $subscription, $sku, $this->payload($dados));
    }

    public function removerItemFixo(string $subscription, string $sku): LegacySubscription
    {
        return $this->legacy->item('delete', $subscription, $sku);
    }

    /** @param array<string, mixed>|null $body */
    private function subscription(HttpMethod $method, string $number, string $suffix = '', ?array $body = null): Subscription
    {
        return Subscription::fromArray($this->request($method, '/subscriptions/'.$this->segment($number).$suffix, body: $body));
    }
}
