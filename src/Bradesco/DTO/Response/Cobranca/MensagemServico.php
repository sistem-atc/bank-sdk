<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Linha de mensagem livre impressa no boleto / devolvida pelo serviço.
 * Origem: POST /boleto/cobranca-consulta/v1/consultar (campo `lista`)
 */
final class MensagemServico implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Texto da mensagem. */
        public readonly ?string $mensagem = null,
    ) {}
}
