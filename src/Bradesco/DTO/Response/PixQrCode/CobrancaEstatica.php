<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Cobrança estática (`cobe`) — QR Code sem expiração, POST `/v1/cobe`.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class CobrancaEstatica implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $txid = null,
        public readonly ?string $valor = null,
        public readonly ?string $chave = null,
        public readonly ?string $solicitacaoPagador = null,
        public readonly ?string $pixCopiaECola = null,
        public readonly ?string $base64 = null,
    ) {}
}
