<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Objeto com os dados da Cobrança gerada para pagamento via QRCode. **Caso não
 * seja possível gerar o Pix no momento, essa propriedade não será retornada.**
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `PixDetalhe`).
 */
final class PixDetalhe implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Txid da Cobrança PIX gerada */
        public readonly ?string $txid = null,
        /** QRCode de cobrança para pagamento */
        public readonly ?string $pixCopiaECola = null,
    ) {}
}
