<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PagarLoteResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PagarLoteResponse`).
 */
final class PagarLoteResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pagamento de um lote específico. */
        public readonly ?string $idLote = null,
        public readonly ?string $status = null,
        /** Identificador do lote */
        public readonly ?string $meuIdentificador = null,
        /** Qtde de pagamentos existentes no lote. */
        public readonly ?int $qtdePagamentos = null,
    ) {}
}
