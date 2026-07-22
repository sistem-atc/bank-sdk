<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Encargo genérico do objeto `valor` de uma COBV — usado por `multa`, `juros` e
 * `abatimento`. `modalidade` (int) segue os domínios da spec e `valorPerc` é
 * valor fixo (R$) ou percentual, conforme a modalidade.
 */
final class ValorEncargo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $modalidade = null,
        public readonly ?string $valorPerc = null,
    ) {}
}
