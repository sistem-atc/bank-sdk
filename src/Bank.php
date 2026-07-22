<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use BadMethodCallException;
use SistemAtc\Banks\Bradesco\Bradesco;
use SistemAtc\Banks\Contracts\BankConnector;
use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Itau\Endpoints\Bolecode\BolecodeMethods;
use SistemAtc\Banks\Itau\Endpoints\Boletos\Boletos;
use SistemAtc\Banks\Itau\Endpoints\PixAutomatico\PixAutomatico;
use SistemAtc\Banks\Itau\Endpoints\RecebimentosPix\RecebimentosPix;
use SistemAtc\Banks\Itau\Endpoints\SaqueTroco\SaqueTroco;
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

    // ── Produtos específicos do Itaú ────────────────────────────────────────
    //
    // Ficam FORA do BankConnector (interface cross-bank) porque cada banco tem
    // um catálogo próprio — forçar o Bradesco a stubar "Bolecode" poluiria o
    // contrato. Acessíveis pela mesma fachada; num banco que não oferece o
    // produto, o erro é explícito em vez de silencioso.

    /** Cobrança por boleto: emissão, instrução, consulta e extrato. */
    public function boletos(BankIntegration $integration): Boletos
    {
        return $this->itau(__FUNCTION__)->boletos($integration);
    }

    /** Recebimentos Pix — arranjo regulatório Bacen (cob/cobv/pix/loc/webhook). */
    public function recebimentosPix(BankIntegration $integration): RecebimentosPix
    {
        return $this->itau(__FUNCTION__)->recebimentosPix($integration);
    }

    /** Pix Automático — recorrência, cobrança recorrente e QR Code. */
    public function pixAutomatico(BankIntegration $integration): PixAutomatico
    {
        return $this->itau(__FUNCTION__)->pixAutomatico($integration);
    }

    /** Bolecode Pix — boleto híbrido com QR Code Pix. */
    public function bolecode(BankIntegration $integration): BolecodeMethods
    {
        return $this->itau(__FUNCTION__)->bolecode($integration);
    }

    /** Pix Saque e Troco — pontos de atendimento e remuneração. */
    public function saqueTroco(BankIntegration $integration): SaqueTroco
    {
        return $this->itau(__FUNCTION__)->saqueTroco($integration);
    }

    /** Garante que o case é Itaú antes de delegar um produto exclusivo dele. */
    private function itau(string $domain): Itau
    {
        $connector = $this->connector();

        if (! $connector instanceof Itau) {
            throw new BadMethodCallException(
                "{$this->name}: o domínio '{$domain}' é exclusivo do Itaú e não está disponível neste banco."
            );
        }

        return $connector;
    }
}
