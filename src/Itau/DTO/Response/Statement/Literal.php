<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `literal` do Extrato Itaú (Account Statement) — histórico/descrição do
 * lançamento. `code` é o código do literal (ver tabela de literais da API),
 * `shortened` a versão curta e `complete` a completa. Saldos não trazem `code`.
 */
final class Literal implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $shortened = null,
        public readonly ?string $complete = null,
    ) {}
}
