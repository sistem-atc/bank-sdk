<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\Cobranca;

use Illuminate\Http\Client\PendingRequest;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Fachada do produto Cobrança (boleto convencional) do Bradesco. São 11
 * microserviços distintos, agrupados aqui por afinidade.
 *
 *   Bank::Bradesco->cobranca($i)->registrar([...]);          // atalho
 *   Bank::Bradesco->cobranca($i)->consultas()->listarPendentes([...]);
 *   Bank::Bradesco->cobranca($i)->webhook()->incluir([...]);
 */
final class Cobranca
{
    public function __construct(private readonly BankIntegration $integration) {}

    /** Registro, alteração, baixa e protesto/negativação. */
    public function titulos(): CobrancaMethods
    {
        return new CobrancaMethods($this->client(), $this->integration);
    }

    /** Consulta de título, 2ª via e as listas (pendentes/liquidados/baixados). */
    public function consultas(): CobrancaConsultaMethods
    {
        return new CobrancaConsultaMethods($this->client(), $this->integration);
    }

    /** Split payment: consulta e manutenção do rateio de crédito. */
    public function split(): CobrancaSplitMethods
    {
        return new CobrancaSplitMethods($this->client(), $this->integration);
    }

    /** Webhook de cobrança (endpoint único; inclusão/alteração/consulta/exclusão). */
    public function webhook(): CobrancaWebhookMethods
    {
        return new CobrancaWebhookMethods($this->client(), $this->integration);
    }

    private function client(): PendingRequest
    {
        return HttpClientFactory::make($this->integration, BradescoHosts::FAMILY_OPEN_API);
    }
}
