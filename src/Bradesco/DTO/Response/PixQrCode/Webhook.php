<?php

declare(strict_types=1);

namespace SistemAtc\Banks\Bradesco\DTO\Response\PixQrCode;

use SistemAtc\Banks\Common\Traits\AutoHydrate;
use SistemAtc\Banks\Common\Traits\CastToArray;
use SistemAtc\Banks\Contracts\DTOInterface;
use SistemAtc\Banks\Contracts\UsesCamelCaseKeys;

/**
 * Webhook de notificação Bacen associado a uma chave Pix — `/v2/webhook/{chave}`.
 *
 * Família PIX (host qrpix) — produto "Pix - geração de QR Code" do Bradesco.
 */
final class Webhook implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $webhookUrl = null,
        public readonly ?string $chave = null,
        public readonly ?string $criacao = null,
    ) {}
}
