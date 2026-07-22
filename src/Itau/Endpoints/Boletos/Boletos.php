<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Endpoints\Boletos;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Itau\Support\ItauHosts;

/**
 * Fachada do produto Boletos Cobrança do Itaú. Reúne os grupos de métodos, cada
 * um construído com o HOST correto — emissão/instrução ficam no host padrão,
 * mas consulta de detalhe e extrato vivem em subdomínios próprios.
 *
 *   Bank::Itau->boletos($i)->emissao()->emitir([...]);
 *   Bank::Itau->boletos($i)->consulta()->consultarDetalhe(...);
 */
final class Boletos
{
    public function __construct(private readonly BankIntegration $integration) {}

    public function emissao(): BoletosMethods
    {
        return new BoletosMethods($this->client('default'), $this->integration);
    }

    public function instrucao(): BoletosInstrucaoMethods
    {
        return new BoletosInstrucaoMethods($this->client('default'), $this->integration);
    }

    public function consulta(): BoletosConsultaMethods
    {
        return new BoletosConsultaMethods($this->client('boletos_consulta'), $this->integration);
    }

    public function extrato(): BoletosExtratoMethods
    {
        return new BoletosExtratoMethods($this->client('boletos_extrato'), $this->integration);
    }

    private function client(string $product): \Illuminate\Http\Client\PendingRequest
    {
        return HttpClientFactory::make($this->integration, ItauHosts::resolve($product, $this->integration));
    }
}
