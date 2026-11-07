<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `ConsultaPixAsyncResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `ConsultaPixAsyncResponse`).
 *
 * @property array<int, HistoricoResponse> $historico
 */
final class ConsultaPixAsyncResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?PixAsyncResponse $transacaoPix = null,
        #[ArrayOf(HistoricoResponse::class)]
        public readonly array $historico = [],
    ) {}
}
