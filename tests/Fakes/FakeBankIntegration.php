<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Tests\Fakes;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Support\ClientCertificate;

/**
 * Implementação de BankIntegration em memória pros testes — o que o host
 * (Bunker) fará com um model real.
 */
final class FakeBankIntegration implements BankIntegration
{
    public ?string $accessToken = null;

    public ?int $tokenExpiresAt = null;

    /** @param array<string, mixed> $settings */
    public function __construct(
        private readonly string $clientId = 'cli',
        private readonly string $clientSecret = 'sec',
        private array $settings = [],
        private readonly ?ClientCertificate $certificate = null,
        private readonly bool $active = true,
        private readonly bool $sandbox = true,
    ) {}

    public function getIntegrationIdentifier(): int|string
    {
        return 1;
    }

    public function getCompanyIdentifier(): int|string
    {
        return 10;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getBankSettings(): array
    {
        return $this->settings + ['token_expires_at' => $this->tokenExpiresAt];
    }

    public function getCertificate(): ?ClientCertificate
    {
        return $this->certificate;
    }

    public function isIntegrationActive(): bool
    {
        return $this->active;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function updateAccessToken(string $accessToken, ?int $expiresIn = null): void
    {
        $this->accessToken = $accessToken;
        $this->tokenExpiresAt = $expiresIn !== null ? time() + $expiresIn : null;
    }
}
