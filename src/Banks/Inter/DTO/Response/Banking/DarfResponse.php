<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `DarfResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `DarfResponse`).
 */
final class DarfResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $quantidadeAprovadores = null,
        /** Autenticação da transação */
        public readonly ?string $autenticacao = null,
        /** Data de pagamento */
        public readonly ?string $dataPagamento = null,
        public readonly ?string $tipoRetorno = null,
        public readonly ?string $codigoSolicitacao = null,
    ) {}
}
