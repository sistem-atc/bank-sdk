<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Nota fiscal relacionada à cobrança. **Essa propriedade só será retornada
 * caso a nota fiscal tenha sido especificada no momento do cadastro da
 * cobrança.**
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `NotaFiscalResponse`).
 */
final class NotaFiscalResponse implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Chave de Acesso da Nota Fiscal. */
        public readonly ?string $chaveNFe = null,
        /** Número da Nota Fiscal. */
        public readonly ?int $numero = null,
        /** Série da Nota Fiscal. */
        public readonly ?int $serie = null,
        /** Data de Emissão da Nota Fiscal. */
        public readonly ?string $dataEmissao = null,
        /** Parcela da Nota Fiscal. */
        public readonly ?int $parcela = null,
        /** Natureza da Operação da Nota Fiscal. */
        public readonly ?string $naturezaOperacao = null,
    ) {}
}
