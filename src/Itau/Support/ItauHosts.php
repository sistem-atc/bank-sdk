<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\Support;

use SistemAtc\Banks\Contracts\BankIntegration;

/**
 * Resolve o HOST de cada produto de API do Itaú. Ao contrário do padrão de um
 * host por banco, o Itaú publica cada produto num subdomínio próprio
 * (api.itau.com.br, pix-pj.api.itau.com, account-statement.api.itau.com,
 * pixautomatico-recebimentos.api.itau.com…). O connector passa a chave do
 * produto e este helper devolve o host certo pro ambiente da integração.
 *
 * As chaves espelham `config('banks.itau.hosts')`. Chave desconhecida cai no
 * host 'default'.
 */
final class ItauHosts
{
    public static function resolve(string $product, BankIntegration $integration): string
    {
        $env = ($integration->isSandbox() || config('banks.sandbox', true)) ? 'sandbox' : 'production';

        $host = config("banks.itau.hosts.{$product}.{$env}")
            ?? config("banks.itau.hosts.default.{$env}");

        return rtrim((string) $host, '/');
    }
}
