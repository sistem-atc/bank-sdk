<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Par codigo/descricao usado em blocos de mensagem.
 *
 * Origem: components.schemas.MessageContent / CoeMessageApiData.
 */
final class MensagemCodigo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo da mensagem. */
        public readonly ?int $code = null,
        /** Texto da mensagem. */
        public readonly ?string $description = null,
    ) {}
}
