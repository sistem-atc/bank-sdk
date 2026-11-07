<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Pix;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Banks\Inter\Support\HttpClientFactory;

/**
 * Fachada da API Pix de recebimento do Inter (`/pix/v2`, padrão Bacen).
 *
 *   Inter::make()->recebimentosPix($i)->cob()->criar([...]);
 *   Inter::make()->recebimentosPix($i)->pixRecebido()->listar($inicio, $fim);
 */
final class RecebimentosPix
{
    public const BASE = '/pix/v2';

    public function __construct(private readonly BankIntegration $integration) {}

    public function cob(): CobMethods
    {
        return new CobMethods($this->client(), $this->integration, self::BASE);
    }

    public function cobv(): CobVMethods
    {
        return new CobVMethods($this->client(), $this->integration, self::BASE);
    }

    public function loteCobV(): LoteCobVMethods
    {
        return new LoteCobVMethods($this->client(), $this->integration, self::BASE);
    }

    public function location(): LocationMethods
    {
        return new LocationMethods($this->client(), $this->integration, self::BASE);
    }

    public function pixRecebido(): PixRecebidoMethods
    {
        return new PixRecebidoMethods($this->client(), $this->integration, self::BASE);
    }

    public function webhook(): WebhookPixMethods
    {
        return new WebhookPixMethods($this->client(), $this->integration, self::BASE);
    }

    public function sandbox(): SandboxPixMethods
    {
        return new SandboxPixMethods($this->client(), $this->integration, self::BASE);
    }

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return HttpClientFactory::make($this->integration, self::BASE);
    }
}
