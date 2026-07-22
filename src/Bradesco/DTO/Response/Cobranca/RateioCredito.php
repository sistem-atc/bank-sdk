<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Cobranca;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Linha de rateio (split payment) de um título — beneficiário, agência/conta
 * de crédito, percentual ou valor e float de liberação.
 * Origem: POST /boleto/cobranca-consulta-split/v1/executar (item de `listaRteio`)
 */
final class RateioCredito implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $cagBnefcRteio = null,
        public readonly ?int $cctaBnefcRteio = null,
        public readonly ?string $vlrPercRteio = null,
        public readonly ?string $ibnefcRteioCredt = null,
        public readonly ?string $pcelaRteioCredt = null,
        public readonly ?int $floatRteioBnefc = null,
    ) {}
}
