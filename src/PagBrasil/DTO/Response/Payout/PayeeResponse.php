<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Payout;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Resposta de addpayee/updatepayee/deletepayee/getpayee. `success` diz que a
 * operação foi registrada — se o favorecido está ATIVO é `payee->status`.
 * (Resposta com success=false vira PagBrasilRequestException antes daqui.)
 */
final class PayeeResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $action = null,
        public readonly bool $success = false,
        public readonly ?Payee $payee = null,
    ) {}
}
