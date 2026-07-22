<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\RecebimentosPix;

use Illuminate\Http\Client\PendingRequest;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Itau\Support\ItauHosts;

/**
 * Fachada do produto Recebimentos Pix (arranjo regulatório Bacen) do Itaú.
 * Todos os grupos rodam no host `pix_recebimentos` (pix-pj.api.itau.com).
 *
 *   Bank::Itau->recebimentosPix($i)->cobImediata()->criar([...]);
 *   Bank::Itau->recebimentosPix($i)->pixRecebido()->consultar($e2eid);
 */
final class RecebimentosPix
{
    public function __construct(private readonly BankIntegration $integration) {}

    public function cobImediata(): CobrancaImediataMethods
    {
        return new CobrancaImediataMethods($this->client(), $this->integration);
    }

    public function cobVencimento(): CobrancaVencimentoMethods
    {
        return new CobrancaVencimentoMethods($this->client(), $this->integration);
    }

    public function pixRecebido(): PixRecebidoMethods
    {
        return new PixRecebidoMethods($this->client(), $this->integration);
    }

    public function location(): LocationMethods
    {
        return new LocationMethods($this->client(), $this->integration);
    }

    public function webhook(): WebhookMethods
    {
        return new WebhookMethods($this->client(), $this->integration);
    }

    public function loteCobV(): LoteCobrancaVencimentoMethods
    {
        return new LoteCobrancaVencimentoMethods($this->client(), $this->integration);
    }

    private function client(): PendingRequest
    {
        return HttpClientFactory::make(
            $this->integration,
            ItauHosts::resolve('pix_recebimentos', $this->integration),
        );
    }
}
