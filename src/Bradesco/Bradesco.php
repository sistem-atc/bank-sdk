<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco;

use SistemAtc\Banks\Bradesco\Endpoints\Dda\DdaMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Payments\PaymentsMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Pix\PixMethods;
use SistemAtc\Banks\Bradesco\Endpoints\Statement\StatementMethods;
use SistemAtc\Banks\Bradesco\Support\BradescoHosts;
use SistemAtc\Banks\Bradesco\Support\HttpClientFactory;
use SistemAtc\Banks\Bradesco\Support\OAuth;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector Bradesco. Cada método de domínio monta um cliente HTTP autenticado
 * (token válido + mTLS) e devolve o grupo de métodos tipado pela interface
 * comum. Resolvido pela fachada `Bank::Bradesco`.
 */
final class Bradesco implements BankConnector
{
    public function auth(BankIntegration $integration): AuthToken
    {
        return OAuth::authenticate($integration);
    }

    public function dda(BankIntegration $integration): DdaMethods
    {
        return new DdaMethods(HttpClientFactory::make($integration), $integration);
    }

    public function statement(BankIntegration $integration): StatementMethods
    {
        return new StatementMethods(HttpClientFactory::make($integration), $integration);
    }

    public function pix(BankIntegration $integration): PixMethods
    {
        return new PixMethods(HttpClientFactory::make($integration, BradescoHosts::FAMILY_PIX), $integration);
    }

    public function payments(BankIntegration $integration): PaymentsMethods
    {
        return new PaymentsMethods(HttpClientFactory::make($integration), $integration);
    }
}
