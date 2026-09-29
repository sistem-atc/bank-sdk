<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Endpoints\PagStream;

use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\PagBrasil\Support\HttpClientFactory;

/**
 * PagStream® — gestão de assinaturas da PagBrasil (precisa ser ativado pela
 * PagBrasil na conta). Agrupa as coleções da API:
 *
 *   Bank::PagBrasil->pagStream($i)->assinaturas()->consultar('P17648804286');
 *   Bank::PagBrasil->pagStream($i)->recorrencias()->pular('P17648804286', 'next');
 *   Bank::PagBrasil->pagStream($i)->envios()->listar('P17648804286');
 *   Bank::PagBrasil->pagStream($i)->produtos()->listar(['status' => 'active']);
 *   Bank::PagBrasil->pagStream($i)->cobrancas()->listar(['status' => 'error']);
 */
final class PagStream
{
    public function __construct(private readonly PagBrasilIntegration $integration) {}

    public function assinaturas(): SubscriptionsMethods
    {
        return new SubscriptionsMethods(
            HttpClientFactory::rest($this->integration),
            $this->integration,
            new LegacyMethods(HttpClientFactory::form($this->integration), $this->integration),
        );
    }

    public function recorrencias(): RecurrencesMethods
    {
        return new RecurrencesMethods(HttpClientFactory::rest($this->integration), $this->integration);
    }

    public function envios(): ShippingsMethods
    {
        return new ShippingsMethods(HttpClientFactory::rest($this->integration), $this->integration);
    }

    public function produtos(): ProductsMethods
    {
        return new ProductsMethods(HttpClientFactory::rest($this->integration), $this->integration);
    }

    public function cobrancas(): ChargesMethods
    {
        return new ChargesMethods(HttpClientFactory::rest($this->integration), $this->integration);
    }
}
