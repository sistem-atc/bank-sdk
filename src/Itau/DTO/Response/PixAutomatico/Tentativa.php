<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\PixAutomatico;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `tentativas` da cobrança recorrente — cada tentativa de liquidação
 * (agendamento normal AGND, não-agendamento NTAG etc.), com seu status e
 * histórico de atualizações.
 */
final class Tentativa implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Atualizacao> $atualizacao */
    public function __construct(
        public readonly ?string $dataLiquidacao = null,
        public readonly ?string $tipo = null,
        public readonly ?string $endToEndId = null,
        public readonly ?string $status = null,
        #[ArrayOf(Atualizacao::class)]
        public readonly array $atualizacao = [],
    ) {}
}
