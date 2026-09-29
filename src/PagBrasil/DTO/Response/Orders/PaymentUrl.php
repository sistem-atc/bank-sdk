<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Orders;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * URL para onde redirecionar o cliente: Link de Pagamento
 * (/api/checkout/add, `<url_payment>`) e 1-Click Pix (/api/pix/1click).
 */
final class PaymentUrl implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly string $urlPayment = '',
    ) {}
}
