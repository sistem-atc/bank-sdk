<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lançamento de extrato Bradesco (Cash Management), pra conciliação bancária.
 * PARCIAL POR DESIGN — campos e nomes a confirmar com a spec real.
 */
final class StatementEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $data = null,
        public readonly ?float $valor = null,
        // 'C' crédito / 'D' débito.
        public readonly ?string $tipo = null,
        public readonly ?string $historico = null,
        public readonly ?string $documento = null,
        public readonly ?float $saldo = null,
    ) {}
}
