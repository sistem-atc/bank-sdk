<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Gateways\PagBrasil\Support;

use SistemAtc\Banks\Contracts\BankIntegration;
use SistemAtc\Banks\Contracts\PagBrasilIntegration;
use SistemAtc\Banks\Contracts\PagBrasilSftpIntegration;
use SistemAtc\Banks\Exceptions\BankAuthenticationException;

/**
 * Garante que a integração entrega o que a PagBrasil exige (pbtoken +
 * secret phrase) antes de qualquer chamada. Falha cedo e explícita: sem isso
 * a PagBrasil responde com texto de erro genérico e HTTP 200.
 */
final class Credentials
{
    public static function of(BankIntegration $integration): PagBrasilIntegration
    {
        if (! $integration instanceof PagBrasilIntegration) {
            throw new BankAuthenticationException(
                'PagBrasil: a integração precisa implementar '.PagBrasilIntegration::class
                .' (pbtoken, secret phrase e signature key).',
                bank: 'pagbrasil',
            );
        }

        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração PagBrasil inativa.', bank: 'pagbrasil');
        }

        if (trim($integration->getPbToken()) === '' || trim($integration->getSecretPhrase()) === '') {
            throw new BankAuthenticationException(
                'PagBrasil: pbtoken e secret phrase são obrigatórios.',
                bank: 'pagbrasil',
            );
        }

        return $integration;
    }

    /** Signature key utilizável, ou null quando o host não a cadastrou. */
    public static function signatureKey(PagBrasilIntegration $integration): ?string
    {
        $key = $integration->getSignatureKey();

        return $key === null || trim($key) === '' ? null : $key;
    }

    /**
     * Credenciais do SFTP de relatórios: usuário e senha próprios (ticket de
     * ativação), independentes do pbtoken. Não exige a API ativa — a conta
     * pode ter só o SFTP.
     */
    public static function sftp(BankIntegration $integration): PagBrasilSftpIntegration
    {
        if (! $integration instanceof PagBrasilSftpIntegration) {
            throw new BankAuthenticationException(
                'PagBrasil: a integração precisa implementar '.PagBrasilSftpIntegration::class
                .' (usuário e senha do SFTP de relatórios).',
                bank: 'pagbrasil',
            );
        }

        if (! $integration->isIntegrationActive()) {
            throw new BankAuthenticationException('Integração PagBrasil inativa.', bank: 'pagbrasil');
        }

        if (trim($integration->getSftpUsername()) === '' || $integration->getSftpPassword() === '') {
            throw new BankAuthenticationException(
                'PagBrasil: usuário e senha do SFTP são obrigatórios para baixar relatórios.',
                bank: 'pagbrasil',
            );
        }

        return $integration;
    }
}
