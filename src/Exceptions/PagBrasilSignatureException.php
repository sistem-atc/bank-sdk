<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Exceptions;

use RuntimeException;

/**
 * A assinatura HMAC-MD5 (ou a secret phrase) de uma resposta/notificação da
 * PagBrasil não confere. Numa notificação isso significa origem não
 * comprovada: não processe o conteúdo.
 */
class PagBrasilSignatureException extends RuntimeException
{
}
