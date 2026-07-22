<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `pix.componentesValor` — composição do valor final de um Pix recebido
 * (juros, multas, descontos, abatimentos, saque e troco) quando o pagamento se
 * refere a uma cobrança com vencimento. Vem nulo quando todos os campos estão
 * zerados.
 */
final class ComponentesValor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ComponenteValor $original = null,
        public readonly ?ComponenteValor $saque = null,
        public readonly ?ComponenteValor $troco = null,
        public readonly ?ComponenteValor $juros = null,
        public readonly ?ComponenteValor $multa = null,
        public readonly ?ComponenteValor $abatimento = null,
        public readonly ?ComponenteValor $desconto = null,
    ) {}
}
