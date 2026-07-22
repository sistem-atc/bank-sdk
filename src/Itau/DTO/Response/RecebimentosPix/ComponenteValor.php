<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Sub-objeto de `pix.componentesValor` (original/saque/troco/juros/multa/
 * abatimento/desconto). A API usa nomes de campo distintos por componente, então
 * este DTO agrega todos — em cada componente apenas os campos pertinentes vêm
 * preenchidos.
 */
final class ComponenteValor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $valor = null,
        public readonly ?string $modalidadeAgente = null,
        public readonly ?string $prestadorDeServicoDeSaque = null,
        public readonly ?string $valorJuros = null,
        public readonly ?string $valorMultaDocumentoCobrancaPix = null,
        public readonly ?string $valorAbatimentoDocumentoCobrancaPix = null,
        public readonly ?string $valorDescontoDocumentoCobrancaPix = null,
    ) {}
}
