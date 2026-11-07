<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Banking;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `PagamentoPixResponse` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PagamentoPixResponse`).
 */
final class PagamentoPixResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $tipoRetorno = null,
        /** Codigo da solicitacao do pagamento Pix */
        public readonly ?string $codigoSolicitacao = null,
        /** Data solicitada para efetivação do pagamento do Pix */
        public readonly ?string $dataPagamento = null,
        /** Data em que foi solicitado a inclusão do pagamento do Pix */
        public readonly ?string $dataOperacao = null,
    ) {}
}
