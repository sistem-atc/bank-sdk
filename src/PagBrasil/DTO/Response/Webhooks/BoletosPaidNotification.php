<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Webhooks;

use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;

/**
 * IPN de boletos pagos (payment_method=B): uma LISTA por notificação, em lote
 * (boleto comum é processado no dia útil seguinte; Boleto Flash no mesmo dia).
 *
 * @property list<BoletoPayment> $boletos
 */
final class BoletosPaidNotification implements Notification
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<BoletoPayment> $boletos */
    public function __construct(
        #[ArrayOf(BoletoPayment::class)]
        public readonly array $boletos = [],
    ) {}
}
