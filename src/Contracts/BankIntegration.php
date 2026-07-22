<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

/**
 * Contrato que o HOST (Bunker) implementa pra fornecer as credenciais de uma
 * conexão bancária ao SDK. Espelha o MarketplaceIntegration do pacote de
 * marketplaces, com o que banco exige e marketplace não:
 *
 *   1. OAuth2 client_credentials — banco não tem "login de usuário": o app
 *      (client_id/client_secret) é a identidade. Não há refresh_token; quando
 *      o access_token expira, reautentica-se com as mesmas credenciais.
 *
 *   2. Certificado mTLS — as APIs de produção de Itaú/Bradesco exigem TLS
 *      mútuo (certificado ICP-Brasil da empresa). O host resolve o .pfx da
 *      empresa (no Bunker, via CompanyCertificate) e expõe path + senha aqui.
 *      Em sandbox o certificado costuma ser dispensado (getCertificatePath
 *      pode devolver null).
 *
 * Multiempresa é nativo: cada CNPJ tem seu app no banco e seu certificado, e
 * cada request carrega a integração da empresa dona da operação.
 */
interface BankIntegration
{
    /** Identificador da integração (linha de conexão bancária no host). */
    public function getIntegrationIdentifier(): int|string;

    /** Identificador da empresa/CNPJ dona da conexão. */
    public function getCompanyIdentifier(): int|string;

    /** client_id do app registrado no portal do banco. */
    public function getClientId(): string;

    /** client_secret do app registrado no portal do banco. */
    public function getClientSecret(): string;

    /** access_token vigente (null se ainda não autenticou ou expirou). */
    public function getAccessToken(): ?string;

    /**
     * Parâmetros extras da conexão que não são credencial pura — agência,
     * conta, chave PIX, convênio de cobrança, etc. Cada Endpoint documenta as
     * chaves que consome.
     *
     * @return array<string, mixed>
     */
    public function getBankSettings(): array;

    /**
     * Path absoluto do certificado mTLS da empresa (.pfx/.p12 ou PEM). null em
     * sandbox ou quando o banco não exige mTLS naquele ambiente.
     */
    public function getCertificatePath(): ?string;

    /** Senha do certificado mTLS (null quando não há certificado). */
    public function getCertificatePassword(): ?string;

    /** A conexão está ativa? (integração desabilitada aborta na fonte). */
    public function isIntegrationActive(): bool;

    /** Rotear pras URLs de homologação desta integração especificamente. */
    public function isSandbox(): bool;

    /**
     * Persiste o access_token recém-obtido no client_credentials. Sem
     * refresh_token — banco reautentica com client_id/secret quando expira.
     */
    public function updateAccessToken(string $accessToken, ?int $expiresIn = null): void;
}
