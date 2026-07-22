<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau;

use SistemAtc\Banks\Itau\Endpoints\Dda\DdaMethods;
use SistemAtc\Banks\Itau\Endpoints\Payments\PaymentsMethods;
use SistemAtc\Banks\Itau\Endpoints\Pix\PixMethods;
use SistemAtc\Banks\Itau\Endpoints\Statement\StatementMethods;
use SistemAtc\Banks\Itau\Support\HttpClientFactory;
use SistemAtc\Banks\Itau\Support\OAuth;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Connector Itau. Cada método de domínio monta um cliente HTTP autenticado
 * (token válido + mTLS) e devolve o grupo de métodos tipado pela interface
 * comum. Resolvido pela fachada `Bank::Itau`.
 */
final class Itau implements BankConnector
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
        return new PixMethods(HttpClientFactory::make($integration), $integration);
    }

    public function payments(BankIntegration $integration): PaymentsMethods
    {
        return new PaymentsMethods(HttpClientFactory::make($integration), $integration);
    }
}
