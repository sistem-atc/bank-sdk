<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Falha de transporte no SFTP de relatórios da PagBrasil (conexão, listagem,
 * download). Falha de credencial ou de chave do servidor é
 * BankAuthenticationException.
 */
class PagBrasilSftpException extends RuntimeException
{
    public readonly string $bank;

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->bank = 'pagbrasil';
    }
}
