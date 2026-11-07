<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Banking;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Banks\Inter\Support\HttpClientFactory;

/**
 * Fachada da API Banking do Inter (`/banking/v2`).
 *
 *   Inter::make()->banking($i)->extrato()->saldo();
 *   Inter::make()->banking($i)->pagamentos()->pagarDarf([...]);
 *   Inter::make()->banking($i)->webhooks()->cadastrar(TipoWebhookBanking::PixPagamento, 'https://...');
 *
 * extrato(), pagamentos() e pix() também saem pelo contrato comum
 * (`Inter::make()->statement()`, `->payments()`, `->pix()`).
 */
final class Banking
{
    public const BASE = '/banking/v2';

    public function __construct(private readonly BankIntegration $integration) {}

    public function extrato(): ExtratoMethods
    {
        return new ExtratoMethods($this->client(), $this->integration, self::BASE);
    }

    public function pagamentos(): PagamentosMethods
    {
        return new PagamentosMethods($this->client(), $this->integration, self::BASE);
    }

    public function pix(): PixPagamentoMethods
    {
        return new PixPagamentoMethods($this->client(), $this->integration, self::BASE);
    }

    public function webhooks(): WebhookBankingMethods
    {
        return new WebhookBankingMethods($this->client(), $this->integration, self::BASE);
    }

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return HttpClientFactory::make($this->integration, self::BASE);
    }
}
