<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `parametros` que ecoa os filtros aplicados numa listagem + a paginação.
 */
final class Parametros implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $inicio = null,
        public readonly ?string $fim = null,
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $status = null,
        public readonly ?Paginacao $paginacao = null,
    ) {}
}
