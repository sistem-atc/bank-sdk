<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Tests\Fakes;

use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\Support\ClientCertificate;

/**
 * PagBrasilIntegration em memória pros testes. A signature key default é a
 * dos exemplos da documentação da PagBrasil, pra os testes reproduzirem as
 * assinaturas publicadas.
 */
final class FakePagBrasilIntegration implements PagBrasilIntegration
{
    public const DOC_KEY = '36d5f7184574caf84f5b48530ac0d690';

    public ?string $accessToken = null;

    public ?int $expiresIn = null;

    public function __construct(
        private readonly string $pbToken = 'PBTOKEN',
        private readonly string $secretPhrase = 'SECRET',
        private readonly ?string $signatureKey = self::DOC_KEY,
        private readonly bool $sandbox = true,
        private readonly bool $active = true,
        private readonly string $clientId = 'cli',
        private readonly string $clientSecret = 'sec',
    ) {}

    public function getPbToken(): string
    {
        return $this->pbToken;
    }

    public function getSecretPhrase(): string
    {
        return $this->secretPhrase;
    }

    public function getSignatureKey(): ?string
    {
        return $this->signatureKey;
    }

    public function getIntegrationIdentifier(): int|string
    {
        return 7;
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

    public function updateAccessToken(string $accessToken, ?int $expiresIn = null): void
    {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
    }
}
