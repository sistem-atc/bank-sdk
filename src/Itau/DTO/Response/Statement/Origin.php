<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\Statement;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Objeto `origin` de um lançamento do Extrato Itaú (Account Statement) — a
 * origem/rastreio da operação. Ex.: `identifier` é o EndToEndId de um Pix,
 * `type`="PIX", `operation`="PIX_EMISSAO".
 */
final class Origin implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $identifier = null,
        public readonly ?string $type = null,
        public readonly ?string $operation = null,
        public readonly ?string $channel = null,
        public readonly ?string $complement = null,
    ) {}
}
