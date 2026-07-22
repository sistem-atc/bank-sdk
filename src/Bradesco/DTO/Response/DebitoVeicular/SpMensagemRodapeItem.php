<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\DebitoVeicular;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Item de `lista` de SpComprovanteTaxaDetalhadoResponse.
 */
final class SpMensagemRodapeItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $codigoMsgRodape = null,  // ex.: 1334
        public readonly ?string $descricaoMsgRodape = null,  // ex.: "Comprovante de pagamento emitido de acordo com a Portaria"
    ) {}
}
