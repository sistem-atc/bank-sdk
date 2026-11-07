<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

use SistemAtc\Banks\Contracts\Endpoints\DdaEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PaymentsEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\PixEndpoint;
use SistemAtc\Banks\Contracts\Endpoints\StatementEndpoint;
use SistemAtc\Banks\Support\AuthToken;

/**
 * Contrato que cada banco (Bradesco, Itaú, Inter) implementa. O enum `Bank`
 * só resolve o connector (`Bank::Itau->connector()`); a regra de cada domínio
 * é da classe do banco. Produtos específicos de cada banco ficam SÓ no connector concreto
 * (`Itau::make()->boletos(...)`), nunca neste contrato nem no enum.
 *
 * Cada método de domínio recebe a integração da empresa e devolve o grupo de
 * métodos daquele domínio, tipado pela INTERFACE comum — é isso que torna os
 * bancos intercambiáveis pra quem consome.
 */
interface BankConnector
{
    public static function make(): self;

    /** Código de compensação FEBRABAN (ex.: '237', '341'). */
    public function code(): string;

    /** Autentica (client_credentials + mTLS) e devolve o token vigente. */
    public function auth(BankIntegration $integration): AuthToken;

    public function dda(BankIntegration $integration): DdaEndpoint;

    public function statement(BankIntegration $integration): StatementEndpoint;

    public function pix(BankIntegration $integration): PixEndpoint;

    public function payments(BankIntegration $integration): PaymentsEndpoint;
}
