<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Um boleto pago na lista do IPN. `amountPaid` pode diferir de `amountDue`
 * (valor impresso) — trate conforme a sua política. `paymentDate` em
 * MM/DD/YYYY (dia do pagamento, sem considerar feriado).
 */
final class BoletoPayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $order = null,
        public readonly ?string $paymentDate = null,
        public readonly ?string $amountPaid = null,
        public readonly ?string $amountDue = null,
        public readonly ?string $paramUrl = null,
    ) {}
}
