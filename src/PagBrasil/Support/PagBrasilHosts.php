<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\Support;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;
use SistemAtc\Banks\Support\Environment;

/**
 * Host da PagBrasil pelo ambiente da integração. Um host só atende todas as
 * APIs (clássica, PagStream v2, OAuth e mocks de sandbox).
 */
final class PagBrasilHosts
{
    public static function resolve(BankIntegration $integration): string
    {
        $environment = Environment::forIntegration($integration);
        $url = config("banks.pagbrasil.base_url.{$environment}");

        if (! is_string($url) || trim($url) === '') {
            // Só acontece se o env/config zerar a URL de propósito.
            throw new BankAuthenticationException(
                "PagBrasil: URL do ambiente '{$environment}' não configurada (PAGBRASIL_BASE_URL / PAGBRASIL_BASE_URL_SANDBOX).",
                bank: 'pagbrasil',
            );
        }

        return rtrim($url, '/');
    }

    public static function isSandbox(BankIntegration $integration): bool
    {
        return Environment::forIntegration($integration) === Environment::SANDBOX;
    }
}
