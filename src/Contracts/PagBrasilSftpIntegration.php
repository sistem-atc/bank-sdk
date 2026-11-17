<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Contracts;

/**
 * Credenciais do SFTP de relatórios da PagBrasil (sfiles.pagbrasil.com).
 *
 * São independentes das credenciais de API (pbtoken + secret phrase): a
 * PagBrasil envia usuário e senha próprios do SFTP no ticket de ativação, e
 * eles não rotacionam. O SDK não guarda nada — o host entrega por chamada,
 * como no resto do pacote.
 *
 * Separado do PagBrasilIntegration de propósito: o lojista pode ter a API sem
 * o SFTP (e vice-versa), e o contrato antigo continua valendo sem os métodos
 * novos.
 */
interface PagBrasilSftpIntegration extends BankIntegration
{
    /** Host do SFTP. Vazio = o default da PagBrasil (sfiles.pagbrasil.com). */
    public function getSftpHost(): string;

    /** Porta do SFTP. 0 = o default (22). */
    public function getSftpPort(): int;

    public function getSftpUsername(): string;

    public function getSftpPassword(): string;

    /**
     * Impressão digital SHA256 da chave do servidor ("SHA256:xxxx…", o mesmo
     * formato do `ssh-keygen -lf`). Com ela, o SDK recusa servidor que não é o
     * da PagBrasil. null = não confere (o guia pede para conferir no primeiro
     * acesso; o comando de diagnóstico do host mostra a impressão).
     */
    public function getSftpHostFingerprint(): ?string;
}
