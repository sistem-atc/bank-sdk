<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `valor.desconto` de uma COBV. Modalidades 1/2 usam a lista
 * `descontoDataFixa` (data + valorPerc); modalidades 3..6 usam `valorPerc`.
 *
 * @property array<int, array<string, mixed>> $descontoDataFixa
 */
final class ValorDesconto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, array<string, mixed>> $descontoDataFixa */
    public function __construct(
        public readonly ?int $modalidade = null,
        public readonly ?string $valorPerc = null,
        public readonly array $descontoDataFixa = [],
    ) {}
}
