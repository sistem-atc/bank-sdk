<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Status e data de vencimento de um item do cadastro.
 *
 * Origem: components.schemas.expirationAlertData.
 */
final class AlertaVencimentoDado implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Situacao do item. */
        public readonly ?int $status = null,
        /** Data de vencimento. */
        public readonly ?string $expiration = null,
    ) {}
}
