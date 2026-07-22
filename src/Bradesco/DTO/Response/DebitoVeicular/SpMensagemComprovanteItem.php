<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `listaMsgs` de SpComprovanteRenavamResponse.
 */
final class SpMensagemComprovanteItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $mensagemComprovante = null,
        public readonly ?int $codigoComprovante = null,
    ) {}
}
