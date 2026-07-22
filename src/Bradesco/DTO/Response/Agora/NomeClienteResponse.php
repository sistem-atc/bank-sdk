<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Agora;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Nome completo do cliente no cadastro da Agora.
 *
 * Origem: POST /managers-cust-aggregated-data-spb/v1/clientfulldata/{cpfCnpj}/{accountCode}
 */
final class NomeClienteResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Nome completo do cliente. */
        public readonly ?string $customerName = null,
    ) {}
}
