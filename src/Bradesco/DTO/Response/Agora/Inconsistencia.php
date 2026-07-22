<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Inconsistencia de negocio retornada pela previdencia.
 *
 * Origem: components.schemas.Inconsistence.
 */
final class Inconsistencia implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo do erro. */
        public readonly ?string $errorCode = null,
        /** Mensagem do erro. */
        public readonly ?string $errorMessage = null,
        /** Campo relacionado. */
        public readonly ?string $errorField = null,
    ) {}
}
