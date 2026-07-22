<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\Payments;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resultado do pagamento de um boleto/conta Bradesco. PARCIAL POR DESIGN —
 * campos a confirmar com a spec real.
 */
final class BoletoPayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $identificador = null,
        public readonly ?string $situacao = null,
        public readonly ?float $valorPago = null,
        public readonly ?string $dataPagamento = null,
        public readonly ?string $autenticacao = null,
    ) {}
}
