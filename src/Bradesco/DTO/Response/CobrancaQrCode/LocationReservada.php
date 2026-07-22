<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\CobrancaQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;

/**
 * Location (id do QR Code Pix) reservado para vincular a um boleto híbrido.
 *
 * Endpoint: POST /boleto-hibrido/cobranca-reserva-location/v1/reservarLoc
 */
final class LocationReservada implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $criacao = null, // Timestamp da criação do ID Location
        public readonly ?string $id = null, // Identificação do Location
        public readonly ?string $location = null, // Location gerado pelo Pix
        public readonly ?string $tipoCob = null, // Tipo do QR Code (Fixo COBV)
    ) {}
}
