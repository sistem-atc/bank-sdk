<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Bloco `response` de status das respostas de posicao.
 *
 * Origem: components.schemas.BaseResponseMessage.
 */
final class RespostaBase implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indica se a operacao foi bem sucedida. */
        public readonly ?bool $success = null,
        /** Codigo de retorno. */
        public readonly ?string $code = null,
        /** Mensagem de retorno. */
        public readonly ?string $message = null,
    ) {}
}
