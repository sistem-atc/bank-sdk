<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lançamento pendente (`pending_events`) do Extrato Itaú — só retorna quando
 * `show_pending_events=true`. Em HTTP 206 (API de pendentes indisponível) o
 * item vem apenas com `error` preenchido (ex.: "Service Unavailable").
 */
final class PendingEvent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?string $operation = null,
        public readonly ?EventDate $date = null,
        public readonly ?Literal $literal = null,
        public readonly ?Amount $amount = null,
        public readonly ?string $error = null,
    ) {}
}
