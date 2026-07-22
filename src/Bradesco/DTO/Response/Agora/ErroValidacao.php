<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Erro de validacao de campo retornado pela previdencia.
 *
 * Origem: components.schemas.ValidationError.
 */
final class ErroValidacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Campo que falhou. */
        public readonly ?string $field = null,
        /** Tipo da restricao. */
        public readonly ?string $restrictType = null,
        /** Mensagem do erro. */
        public readonly ?string $message = null,
        /** Tamanho minimo aceito. */
        public readonly ?int $minLength = null,
        /** Tamanho maximo aceito. */
        public readonly ?int $maxLength = null,
    ) {}
}
