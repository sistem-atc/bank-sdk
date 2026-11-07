<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Itau\Endpoints\SaqueTroco;

use Illuminate\Http\Client\PendingRequest;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Banks\Itau\Support\ItauHosts;

/**
 * Fachada do produto Pix Saque e Troco do Itaú (host `pix_recebimentos`,
 * pix-pj.api.itau.com). Reúne o CRUD de pontos de atendimento e as consultas de
 * remuneração.
 *
 *   Itau::make()->saqueTroco($i)->pontos()->listar([...]);
 *   Itau::make()->saqueTroco($i)->remuneracao()->consolidados([...]);
 */
final class SaqueTroco
{
    public function __construct(private readonly BankIntegration $integration) {}

    public function pontos(): PontosAtendimentoMethods
    {
        return new PontosAtendimentoMethods($this->client(), $this->integration, ItauHosts::resolve('pix_recebimentos', $this->integration));
    }

    public function remuneracao(): RemuneracaoMethods
    {
        return new RemuneracaoMethods($this->client(), $this->integration, ItauHosts::resolve('pix_recebimentos', $this->integration));
    }

    private function client(): PendingRequest
    {
        return HttpClientFactory::make(
            $this->integration,
            ItauHosts::resolve('pix_recebimentos', $this->integration),
        );
    }
}
