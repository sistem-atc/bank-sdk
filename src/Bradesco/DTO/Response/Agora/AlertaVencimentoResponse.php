<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Situacao cadastral: vencimento do cadastro e do perfil.
 *
 * Origem: POST /managers-expiration-alert/v1/expirationAlert/{cpfCnpj}/{cblc}
 */
final class AlertaVencimentoResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Vencimento do cadastro. */
        public readonly ?AlertaVencimentoDado $registration = null,
        /** Vencimento do perfil de investidor. */
        public readonly ?AlertaVencimentoDado $profile = null,
    ) {}
}
