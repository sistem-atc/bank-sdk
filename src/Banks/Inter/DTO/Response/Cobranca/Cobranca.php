<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `Cobranca` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `Cobranca`).
 */
final class Cobranca implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $codigoSolicitacao = null,
        /** Codigo da cobrança (seu número) */
        public readonly ?string $seuNumero = null,
        /** Possíveis situações de uma cobranca. */
        public readonly ?string $situacao = null,
        /** Data que a situação da cobrança sofreu alteração. */
        public readonly ?string $dataSituacao = null,
        /** Data e hora em que a cobrança foi emitida. */
        public readonly ?string $dataEmissao = null,
        public readonly ?string $dataVencimento = null,
        /** Valor original da cobrança. */
        public readonly ?string $valorNominal = null,
        public readonly ?string $valorTotalRecebido = null,
        public readonly ?string $origemRecebimento = null,
        public readonly ?string $tipoCobranca = null,
        public readonly ?PagadorCobranca $pagador = null,
    ) {}
}
