<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Lote de cobranças com vencimento — resposta de `GET /lotecobv/{id}`. Cada
 * cobrança do lote (`cobsv`) segue a shape de {@see Cobranca}; quando a criação
 * de uma cobrança do lote é negada, o item traz um objeto `problema` (RFC 7807),
 * exposto aqui como array cru.
 *
 * @property array<int, Cobranca> $cobsv
 */
final class LoteCobranca implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int, Cobranca> $cobsv */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $descricao = null,
        public readonly ?string $criacao = null,
        public readonly ?string $status = null,
        #[ArrayOf(Cobranca::class)]
        public readonly array $cobsv = [],
    ) {}
}
