<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `HistoricoResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `HistoricoResponse`).
 */
final class HistoricoResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Status atual da transação Pix * `CRIADO` * `AGUARDANDO_APROVACAO` * `APROVADO` * `REPROVADO` * `EXPIRADO` * `CANCELADO` * `FALHA` * `AGENDADO` * `PAGO… */
        public readonly ?string $status = null,
        public readonly ?string $dataHoraEvento = null,
    ) {}
}
