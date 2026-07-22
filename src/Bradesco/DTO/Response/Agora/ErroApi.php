<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Erro individual do bloco `errors` das respostas de posicao.
 *
 * Origem: components.schemas.ErrorMessages.
 */
final class ErroApi implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo do erro. */
        public readonly ?int $code = null,
        /** Titulo do erro. */
        public readonly ?string $title = null,
        /** Detalhe do erro. */
        public readonly ?string $detail = null,
    ) {}
}
