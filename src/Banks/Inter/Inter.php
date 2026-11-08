<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter;

use BadMethodCallException;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Banks\Inter\Endpoints\Banking\Banking;
use SistemAtc\Banks\Banks\Inter\Endpoints\Banking\ExtratoMethods;
use SistemAtc\Banks\Banks\Inter\Endpoints\Banking\PagamentosMethods;
use SistemAtc\Banks\Banks\Inter\Endpoints\Banking\PixPagamentoMethods;
use SistemAtc\Banks\Banks\Inter\Endpoints\Cobranca\Cobranca;
use SistemAtc\Banks\Banks\Inter\Endpoints\Pix\RecebimentosPix;
use SistemAtc\Banks\Banks\Inter\Support\OAuth;
use SistemAtc\Banks\Banks\Inter\Webhooks\InterWebhooks;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector do Banco Inter PJ ("APIs Inter Empresas").
 *
 * Domínios comuns (BankConnector): auth, statement, pix, payments — via
 * `Bank::Inter`. Produtos do Inter, direto aqui:
 *
 *   Inter::make()->banking($i)          extrato, saldo, pagamentos, Pix de saída, webhooks
 *   Inter::make()->cobranca($i)         boleto com Pix (Cobrança v3)
 *   Inter::make()->recebimentosPix($i)  cob, cobv, lote, location, Pix recebido, devolução
 *   Inter::make()->webhooks($i)         cadastro dos avisos de todas as APIs, na rota do SDK
 *
 * Um host por ambiente serve token e APIs; o ambiente vem da integração
 * (isSandbox()). mTLS obrigatório nos dois ambientes.
 *
 * Fora por enquanto: Pix Automático (`/pix/v2/rec`…) e a Cobrança v2, que o
 * Inter mantém só por legado.
 */
final class Inter implements BankConnector
{
    public static function make(): self
    {
        return new self();
    }

    public function code(): string
    {
        return '077';
    }

    public function auth(BankIntegration $integration): AuthToken
    {
        return OAuth::authenticate($integration);
    }

    /**
     * O Inter não publica API de DDA. Explícito em vez de path chutado.
     */
    public function dda(BankIntegration $integration): never
    {
        throw new BadMethodCallException(
            'Inter: não há API de DDA no catálogo das APIs Inter Empresas. '
            .'Para pagar boletos use payments()->pagarBoleto().'
        );
    }

    public function statement(BankIntegration $integration): ExtratoMethods
    {
        return $this->banking($integration)->extrato();
    }

    public function pix(BankIntegration $integration): PixPagamentoMethods
    {
        return $this->banking($integration)->pix();
    }

    public function payments(BankIntegration $integration): PagamentosMethods
    {
        return $this->banking($integration)->pagamentos();
    }

    public function banking(BankIntegration $integration): Banking
    {
        return new Banking($integration);
    }

    public function cobranca(BankIntegration $integration): Cobranca
    {
        return new Cobranca($integration);
    }

    public function recebimentosPix(BankIntegration $integration): RecebimentosPix
    {
        return new RecebimentosPix($integration);
    }

    /**
     * Webhooks de todas as APIs do Inter apontando pra rota que o SDK publica
     * (ver config banks.webhooks e o evento BankWebhookReceived).
     */
    public function webhooks(BankIntegration $integration): InterWebhooks
    {
        return new InterWebhooks($integration);
    }
}
