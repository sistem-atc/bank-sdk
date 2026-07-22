<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\PixAutomatico;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Itau\Support\ItauHosts;

/**
 * Fachada do produto Pix Automático do Itaú. A recorrência e a cobrança
 * recorrente ficam no host `pix_automatico_rec`; a emissão de QR Code vive num
 * host próprio (`pix_automatico_qr`).
 *
 *   Bank::Itau->pixAutomatico($i)->recorrencias()->criar([...]);
 *   Bank::Itau->pixAutomatico($i)->qrCode()->criar([...]);
 */
final class PixAutomatico
{
    public function __construct(private readonly BankIntegration $integration) {}

    public function recorrencias(): RecorrenciaMethods
    {
        return new RecorrenciaMethods($this->client('pix_automatico_rec'), $this->integration);
    }

    public function cobrancas(): CobrancaRecorrenteMethods
    {
        return new CobrancaRecorrenteMethods($this->client('pix_automatico_rec'), $this->integration);
    }

    public function qrCode(): QrCodeMethods
    {
        return new QrCodeMethods($this->client('pix_automatico_qr'), $this->integration);
    }

    private function client(string $product): \Illuminate\Http\Client\PendingRequest
    {
        return HttpClientFactory::make($this->integration, ItauHosts::resolve($product, $this->integration));
    }
}
