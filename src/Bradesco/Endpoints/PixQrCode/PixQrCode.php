<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\Endpoints\PixQrCode;

use Illuminate\Http\Client\PendingRequest;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Fachada do produto Pix - geração de QR Code do Bradesco (arranjo regulatório
 * Bacen). TODO o produto roda na família PIX (host qrpix + autorizador
 * /v2/oauth/token com Basic Auth).
 *
 *   Bank::Bradesco->pixQrCode($i)->cobrancaImediata()->criar([...]);
 *   Bank::Bradesco->pixQrCode($i)->pixRecebido()->consultar($e2eid);
 */
final class PixQrCode
{
    public function __construct(private readonly BankIntegration $integration) {}

    public function cobrancaImediata(): CobrancaImediataMethods
    {
        return new CobrancaImediataMethods($this->client(), $this->integration);
    }

    public function cobrancaVencimento(): CobrancaVencimentoMethods
    {
        return new CobrancaVencimentoMethods($this->client(), $this->integration);
    }

    public function cobrancaEstatica(): CobrancaEstaticaMethods
    {
        return new CobrancaEstaticaMethods($this->client(), $this->integration);
    }

    public function location(): LocationMethods
    {
        return new LocationMethods($this->client(), $this->integration);
    }

    public function pixRecebido(): PixRecebidoMethods
    {
        return new PixRecebidoMethods($this->client(), $this->integration);
    }

    public function webhook(): WebhookMethods
    {
        return new WebhookMethods($this->client(), $this->integration);
    }

    private function client(): PendingRequest
    {
        return HttpClientFactory::make($this->integration, BradescoHosts::FAMILY_PIX);
    }
}
