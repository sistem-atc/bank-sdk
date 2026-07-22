<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Itau\DTO\Response\RecebimentosPix;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Location (`/loc`) — código reaproveitável associado a QR Codes. Serve tanto
 * como recurso do endpoint `/loc/{id}` quanto como o objeto `loc` aninhado numa
 * cobrança. `tipoCob` ∈ {cob, cobv}.
 */
final class Location implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $location = null,
        public readonly ?string $tipoCob = null,
        public readonly ?string $criacao = null,
        public readonly ?string $txid = null,
    ) {}
}
