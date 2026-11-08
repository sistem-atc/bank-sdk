<?php

declare(strict_types=1);

use SistemAtc\Banks\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// ── Asaas ──────────────────────────────────────────────────────────────────

/**
 * Integração Asaas em sandbox. Os testes (vindos do asaas-laravel) usam o host
 * antigo do sandbox, então o helper fixa esse host.
 */
function asaasIntegration(?\SistemAtc\Banks\Tests\Fakes\FakeAsaasIntegration $integration = null): \SistemAtc\Banks\Tests\Fakes\FakeAsaasIntegration
{
    config(['banks.asaas.base_url.sandbox' => 'https://sandbox.asaas.com/api/v3']);

    return $integration ?? new \SistemAtc\Banks\Tests\Fakes\FakeAsaasIntegration();
}

/** @return array<mixed> fixture JSON de tests/Fixtures/Asaas */
function asaasFixture(string $path): array
{
    $full = __DIR__."/Fixtures/Asaas/{$path}.json";

    if (! file_exists($full)) {
        throw new InvalidArgumentException("Fixture não encontrada: {$full}");
    }

    return json_decode((string) file_get_contents($full), true, flags: JSON_THROW_ON_ERROR);
}
