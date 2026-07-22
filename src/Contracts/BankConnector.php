<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Contrato que cada banco (Bradesco, Itaú) implementa. É o alvo por trás da
 * fachada enum `Bank`: `Bank::Bradesco` resolve pra um BankConnector e delega.
 *
 * Cada método de domínio recebe a integração da empresa e devolve o grupo de
 * métodos daquele domínio, tipado pela INTERFACE comum — é isso que torna os
 * bancos intercambiáveis pra quem consome.
 */
interface BankConnector
{
    /** Autentica (client_credentials + mTLS) e devolve o token vigente. */
    public function auth(BankIntegration $integration): AuthToken;

    public function dda(BankIntegration $integration): DdaEndpoint;

    public function statement(BankIntegration $integration): StatementEndpoint;

    public function pix(BankIntegration $integration): PixEndpoint;

    public function payments(BankIntegration $integration): PaymentsEndpoint;
}
