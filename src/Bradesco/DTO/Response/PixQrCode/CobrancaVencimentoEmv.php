<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Cobrança com vencimento na variante EMV — payload `emv` + PNG `base64` junto da cobrança.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class CobrancaVencimentoEmv implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $emv = null,
        public readonly ?string $base64 = null,
        public readonly ?CobrancaVencimento $cobv = null,
    ) {}
}
