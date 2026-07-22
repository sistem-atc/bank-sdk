<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

use SistemAtc\Banks\Support\ClientCertificate;

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
 *      mútuo. O formato varia por banco: o Itaú entrega um "certificado
 *      dinâmico" como par PEM `.crt`+`.key` separado; o Bradesco/e-CNPJ A1 usa
 *      PKCS#12 (.pfx). O host resolve isso e devolve um ClientCertificate por
 *      getCertificate(). Em sandbox o certificado costuma ser dispensado
 *      (getCertificate() pode devolver null).
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
     * Certificado mTLS da empresa (PEM `.crt`+`.key` do Itaú, ou PKCS#12 do
     * Bradesco). null em sandbox ou quando o banco não exige mTLS no ambiente.
     */
    public function getCertificate(): ?ClientCertificate;

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
