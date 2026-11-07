<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Objeto com os dados da Cobrança gerada para pagamento via código de
 * barras/linha digitável. Caso a cobrança esteja com a situação
 * EM_PROCESSAMENTO, essa propriedade poderá não ser retornada.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `BoletoDetalhe`).
 */
final class BoletoDetalhe implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Nosso Número, atribuído automaticamente ao longo da inclusão do título. Caso a cobrança esteja com a situação EM_PROCESSAMENTO, essa campo poderá não … */
        public readonly ?string $nossoNumero = null,
        /** Dígitos que compõem o código de barras do boleto (44 posições preenchidas) */
        public readonly ?string $codigoBarras = null,
        /** Dígitos que compõem a linha digitável do boleto, sem formatação (47 posições preenchidas) */
        public readonly ?string $linhaDigitavel = null,
    ) {}
}
