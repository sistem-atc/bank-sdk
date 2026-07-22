<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Exceptions;

use RuntimeException;

/**
 * Falha de autenticação/credencial contra o banco: integração inativa,
 * client_id/secret inválidos, certificado mTLS ausente ou recusado, grant
 * client_credentials negado. O `$bank` identifica qual banco falhou.
 */
class BankAuthenticationException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $bank = '',
    ) {
        parent::__construct($message);
    }
}
