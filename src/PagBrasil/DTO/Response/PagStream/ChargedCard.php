<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Response\PagStream;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/** Cartão tentado numa cobrança (só tentativas de cartão). */
final class ChargedCard implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cardToken = null,
        public readonly ?string $status = null,
        public readonly ?string $errorCode = null,
    ) {}
}
