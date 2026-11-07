<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Valores monetários.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobVValor`).
 */
final class CobVValor implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Valor original da cobrança. */
        public readonly ?string $original = null,
        /** Multa aplicada à cobrança */
        public readonly ?CobVValorMulta $multa = null,
        /** Juro aplicado à cobrança */
        public readonly ?CobVValorMulta $juros = null,
        /** Abatimento aplicado à cobrança */
        public readonly ?CobVValorMulta $abatimento = null,
        /** Descontos aplicados à cobrança */
        public readonly ?CobVValorDesconto $desconto = null,
    ) {}
}
