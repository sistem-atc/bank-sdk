<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Boletos;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta paginada da consulta de detalhe do boleto — `GET /boletoscash/v2/
 * boletos` (e `boletos_search`). A lista de boletos vem em `data[]` e a
 * paginação em `page`.
 *
 * @property list<BoletoDetalhe> $data
 */
final class BoletoConsultaResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<BoletoDetalhe> $data */
    public function __construct(
        #[ArrayOf(BoletoDetalhe::class)]
        public readonly array $data = [],
        /** @var array<string, mixed>|null */
        public readonly ?array $page = null,
    ) {}
}
