<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `EfetuarPagamentoResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `EfetuarPagamentoResponse`).
 */
final class EfetuarPagamentoResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $quantidadeAprovadores = null,
        public readonly ?string $dataAgendamento = null,
        public readonly ?string $statusPagamento = null,
        public readonly ?string $codigoTransacao = null,
    ) {}
}
