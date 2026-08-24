<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\BoletoNegociado;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada de `GET /boleto/v1/boletos` — a lista vem em `data` e a
 * paginação num objeto `page`.
 *
 * @property list<BoletoNegociado> $itens
 * @property array<string, mixed> $page
 */
final class BoletosNegociadosList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<BoletoNegociado>  $itens
     * @param  array<string, mixed>  $page
     */
    public function __construct(
        #[ArrayOf(BoletoNegociado::class)]
        public readonly array $itens = [],
        public readonly array $page = [],
    ) {}
}
