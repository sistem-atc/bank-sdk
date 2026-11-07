<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\Endpoints\Cobranca;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Banks\Inter\Support\HttpClientFactory;

/**
 * Fachada da Cobrança v3 (BolePix) do Inter.
 *
 *   Inter::make()->cobranca($i)->boletos()->emitir([...]);
 *   Inter::make()->cobranca($i)->webhook()->cadastrar('https://...');
 */
final class Cobranca
{
    public const BASE = '/cobranca/v3';

    public function __construct(private readonly BankIntegration $integration) {}

    public function boletos(): CobrancaMethods
    {
        return new CobrancaMethods(HttpClientFactory::make($this->integration, self::BASE), $this->integration, self::BASE);
    }

    public function webhook(): WebhookCobrancaMethods
    {
        return new WebhookCobrancaMethods(HttpClientFactory::make($this->integration, self::BASE), $this->integration, self::BASE);
    }
}
