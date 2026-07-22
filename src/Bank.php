<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use SistemAtc\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Itau\Itau;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Entrypoint do SDK bancário. É um enum pra permitir o uso encadeado a partir
 * do case, sem `new`:
 *
 *   Bank::Bradesco->auth($integration);
 *   Bank::Bradesco->dda($integration)->consultar([...]);
 *   Bank::Itau->statement($integration)->periodo('2026-07-01', '2026-07-31');
 *   Bank::Itau->pix($integration)->pagar([...]);
 *
 * O case resolve pro BankConnector concreto (via `connector()`) e os métodos
 * de fachada delegam. Como cada método é tipado pela INTERFACE de domínio,
 * trocar `Bradesco` por `Itau` não muda o código do consumidor.
 *
 * Sobre `$integration`: banco não tem "sessão de usuário" — a identidade é o
 * app (client_id/secret) + o certificado mTLS da empresa. Esses dados chegam
 * pela implementação de BankIntegration que o host passa em cada chamada,
 * igual ao MarketplaceIntegration do pacote de marketplaces.
 */
enum Bank
{
    case Bradesco;
    case Itau;

    /** Resolve o connector concreto do banco. */
    public function connector(): BankConnector
    {
        return match ($this) {
            self::Bradesco => new Bradesco(),
            self::Itau => new Itau(),
        };
    }

    /** Código de compensação FEBRABAN (útil pro CNAB e conciliação). */
    public function code(): string
    {
        return match ($this) {
            self::Bradesco => '237',
            self::Itau => '341',
        };
    }

    public function auth(BankIntegration $integration): AuthToken
    {
        return $this->connector()->auth($integration);
    }

    public function dda(BankIntegration $integration): DdaEndpoint
    {
        return $this->connector()->dda($integration);
    }

    public function statement(BankIntegration $integration): StatementEndpoint
    {
        return $this->connector()->statement($integration);
    }

    public function pix(BankIntegration $integration): PixEndpoint
    {
        return $this->connector()->pix($integration);
    }

    public function payments(BankIntegration $integration): PaymentsEndpoint
    {
        return $this->connector()->payments($integration);
    }
}
