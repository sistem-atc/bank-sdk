<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Schema `CobrancaDetalhadaResponseBody` da API Inter.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobrancaDetalhadaResponseBody`).
 */
final class CobrancaDetalhadaResponseBody implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CobrancaDetalhe $cobranca = null,
        /** Objeto com os dados da Cobrança gerada para pagamento via código de barras/linha digitável. Caso a cobrança esteja com a situação EM_PROCESSAMENTO, es… */
        public readonly ?BoletoDetalhe $boleto = null,
        /** Objeto com os dados da Cobrança gerada para pagamento via QRCode. **Caso não seja possível gerar o Pix no momento, essa propriedade não será retornada… */
        public readonly ?PixDetalhe $pix = null,
        /** Nota fiscal relacionada à cobrança. **Essa propriedade só será retornada caso a nota fiscal tenha sido especificada no momento do cadastro da cobrança… */
        public readonly ?NotaFiscalResponse $notaFiscal = null,
    ) {}
}
