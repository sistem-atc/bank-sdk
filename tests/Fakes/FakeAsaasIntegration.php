<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Tests\Fakes;

use SistemAtc\Banks\Contracts\AsaasIntegration;
use SistemAtc\Banks\Support\ClientCertificate;

/** AsaasIntegration em memória pros testes. */
final class FakeAsaasIntegration implements AsaasIntegration
{
    public function __construct(
        private readonly string $apiKey = 'minha-chave-secreta',
        private readonly ?string $webhookToken = 'token-de-teste',
        private readonly bool $active = true,
        private readonly bool $sandbox = true,
        private readonly int|string $id = 1,
    ) {}

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getWebhookToken(): ?string
    {
        return $this->webhookToken;
    }

    public function getIntegrationIdentifier(): int|string
    {
        return $this->id;
    }

    public function getCompanyIdentifier(): int|string
    {
        return 10;
    }

    public function getClientId(): string
    {
        return '';
    }

    public function getClientSecret(): string
    {
        return '';
    }

    public function getAccessToken(): ?string
    {
        return null;
    }

    public function getBankSettings(): array
    {
        return [];
    }

    public function getCertificate(): ?ClientCertificate
    {
        return null;
    }

    public function isIntegrationActive(): bool
    {
        return $this->active;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function updateAccessToken(string $accessToken, ?int $expiresIn = null): void {}
}
