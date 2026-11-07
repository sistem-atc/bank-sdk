<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Banks\Inter\DTO\Response\Pix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * valores monetários referentes à cobrança.
 *
 * Gerado a partir da spec OpenAPI oficial do Inter (schema `CobValor`).
 */
final class CobValor implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Valor original da cobrança. */
        public readonly ?string $original = null,
        /** Trata-se de um campo que determina se o valor final do documento pode ser alterado pelo pagador. Na ausência desse campo, assume-se que não se pode al… */
        public readonly ?int $modalidadeAlteracao = null,
        /** É uma estrutura opcional relacionada ao conceito de recebimento de numerário. Apenas um agrupamento por vez é permitido, quando há `saque` não há `tro… */
        public readonly ?Retirada $retirada = null,
    ) {}
}
