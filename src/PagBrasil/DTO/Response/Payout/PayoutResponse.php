<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\Payout;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Payout registrado (addpayout). O resultado final chega pelo webhook
 * (successpayout / failpayout), correlacionado por `id`.
 */
final class PayoutResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $action = null,
        public readonly bool $success = false,
        public readonly ?string $id = null,
    ) {}
}
